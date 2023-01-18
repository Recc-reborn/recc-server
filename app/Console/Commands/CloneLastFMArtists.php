<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Artist;
use App\Models\Track;
use App\Models\Tag;
use App\Services\LastFMService;
use Http\Client\Exception;

class CloneLastFMArtists extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'lfm:clone-artists';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Clone artists from LastFM into the database.';


    /**
     * How many pages to clone?
     * Limited by LastFM to 10,000 (500 * 20)
     *
     * @var int
     */
    protected $artistPageLimit = 20;

    /**
     * How many artists per page?
     *
     * @var int
     */
    protected $artistsPerPage = 500;

    /**
     * How many tracks per artist?
     * @var int
     */
    protected $tracksPerArtist = 30;

    /**
     * How many tags per track
     * @var int
     */
    protected $tagsByTrack = 3;

    /**
     * How many times to retry a request, total?
     *
     * @var int
     */
    protected $retryLimit = 3;

    protected $lastFM;

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(LastFMService $lastFM): int
    {
        $this->lastFM = $lastFM;
        $initWarn = "
            [!IMPORTANT!] This command should NOT be run too often as it is a 
            pretty heavy on LastFM's servers and we don't want to get banned!
        ";
        $initWarn = str_replace(array("\r", "\n", "    ", "\t"), "", $initWarn);
        $this->warn($initWarn);

        $this->fetchArtists();

        return 0;
    }

    /*
     * Fetches tracks from LastFM
     */
    protected function fetchTracks(mixed $pageArtists): int
    {
        foreach ($pageArtists as $artist) {
            try {
                $tracks = $this->getTrackPage($artist->name);
                $tracks = $tracks->toptracks->track;
                $this->addTracksToDatabase($tracks, $artist->name);
                return 0;
            } catch (Exception $e) {
                return 1;
            }
        }
    }

    /**
     * Fetches top tracks' tags from LastFM
     */
    protected function fetchTags(string $artist, string $track): array
    {
        $tags = $this->getTrackInfo($artist, $track);
        $tags = $tags->track->toptags->tag;
        $tagIds = array();
        foreach ($tags as $tag) {
            array_push($tagIds, $this->addtagsToDatabase($tag));
        }
        return $tagIds;
    }


    /**
     * Fetches artists from LastFM
     */
    protected function fetchArtists(): int
    {
        $totalRetries = 0;
        // whatever happens first: artist limit reached or an error response
        for ($page = 1; $page <= $this->artistPageLimit; $page++) {
            $zeroFilledPageNumber = str_pad((string) $page, 2, "0", STR_PAD_LEFT);
            $this->info("Retrieving page $zeroFilledPageNumber / $this->artistPageLimit");
            try {
                $result = $this->getArtistPage($page);
                $pageArtists = $result->artists->artist;
                $this->fetchTracks($pageArtists);

                $this->addArtistsToDatabase($pageArtists);

                $artistCount = count($pageArtists);

                if (!$pageArtists || $artistCount < $this->artistsPerPage) {
                    // not enough artists to complete page, we'll take it as if
                    // we retrieved all retrievable artists
                    $this->warn("Not enough artists ($artistCount) to fill a page ($this->artistsPerPage). Leaving seeder.");
                    return 0;
                }
            } catch (Exception $e) {
                // error response
                if ($totalRetries < $this->retryLimit) {
                    $this->warn($e->getMessage());
                    $this->warn("Error retrieving top artists. Trying again ($totalRetries / $this->retryLimit).");
                    // try getting this page again
                    $totalRetries++;
                    $page--;
                    continue;
                }

                $this->error("Too many errors ($totalRetries / $this->retryLimit). Leaving seeder.");
                // unless we checked this page too many times already?
                return 1;
            }
        }
        return 0;
    }

    /**
     * Get a page's worth of artists from LastFM
     *
     * @return mixed[]
     */
    protected function getArtistPage(int $page): mixed
    {
        return $this->lastFM->call(
            'chart.getTopArtists',
            ['limit' => $this->artistsPerPage, 'page' => $page]
        );
    }

    /**
     * Get a page's worth of tracks from a specefic artist
     * @param string $artist
     */
    protected function getTrackPage(string $artist): mixed
    {
        return $this->lastFM->call(
            'artist.gettoptracks',
            ['limit' => $this->tracksPerArtist,
            'artist' => $artist]
        );
    }

    /**
     * Fetches info of a specefic song
     *
     * @return void
     */
    protected function getTrackInfo(string $artist, string $track): mixed
    {
        return $this->lastFM->call(
            'track.getInfo',
            ['track' => $track,
            'artist' => $artist]
        );
    }

    /**
     * Takes artists as they come from LastFM and creates an \App\Models\Artist
     * instance out of each of them.
     *
     * @param mixed[] $artists
     */
    public function addArtistsToDatabase($artists): void
    {
        // LastFM responses are weird
        foreach ($artists as $artist) {
            if (Artist::where('last_fm_url', $artist->url)->exists()) {
                continue;
            }

            Artist::create([
                'name' => $artist->name,
                'mbid' => $artist->mbid,
                'listeners' => $artist->listeners,
                // last image is usually the largest
                'image_url' => $artist->image[array_key_last($artist->image)]->{'#text'},
                'last_fm_url' => $artist->url,
            ]);
        }
    }

    /**
     * Creates a \App\Models\Tag
     *
     * @param mixed[] tags
     */
    public function addtagsToDatabase($tag): int
    {
        $tagExists = Tag::where('name', $tag->name)->exists();
        if (empty($tagExists)) {
            $tag = Tag::create([
                'name' => $tag->name,
                'url' => $tag->url
            ]);
            return $tag->id;
        }
        $id = Tag::where('name', $tag->name)->value('id');
        return $id;
    }

    /**
     * Creates a \App\Models\Track
     *
     * @param mixed[] tracks
     */
    public function addTracksToDatabase($trackPage, $artist): void
    {
        foreach ($trackPage as $track) {
            $trackInfo = $this->getTrackInfo($artist, $track->name);
            $trackInfo = $trackInfo->track;

            $tags = $this->fetchTags($artist, $trackInfo->name);
            if (empty($trackInfo->album)) {
                $album = null;
                $album_art_url = null;
            } else {
                $album = $trackInfo->album->title;
                $album_art_url = (string) $trackInfo->album->image[array_key_last($trackInfo->album->image)]->{'#text'};
            }

            $track = new Track;
            $track->title = $trackInfo->name;
            $track->artist = $artist;
            $track->duration = $trackInfo->duration;
            $track->album = $album;
            $track->album_art_url = $album_art_url;
            $track->url = (string) $trackInfo->url;
            $track->save();

            $track->tags()->sync($tags);
        }
    }
}
