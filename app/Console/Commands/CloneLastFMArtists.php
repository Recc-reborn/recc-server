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
     * How many artists per page?
     *
     * @var int
     */
    protected $minArtistPerPage = 50;

    /**
     * How many tracks per artist?
     * @var int
     */
    protected $tracksPerArtist = 4;

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
     * @param mixed $pageArtists
     */
    protected function fetchTracks($pageArtists): void
    {
        foreach ($pageArtists as $artist) {
            $tracks = $this->getTrackPage($artist->name);
            if (!isset($tracks->toptracks->track)) {
                $this->error("'$artist->name'\'s tracks couldn't get fetched");
                continue;
            }
            $tracks = $tracks->toptracks->track;
            $this->addTracksToDatabase($tracks, $artist->name);
        }
    }

    /**
     * Gets a $trackInfo variable from last.fm's track.getInfo method and returns
     * an array with all the song's tags.
     * @param mixed $trackInfo
     */
    protected function getTagIds($trackInfo): array
    {
        $tagIds = array();
        $tags = $trackInfo->track->toptags->tag;
        $tagsStr = implode(',', array_column($tags, 'name'));

        foreach ($tags as $tag) {
            if (trim(strtolower($tag->name)) !== trim(strtolower($trackInfo->track->artist->name))) {
                array_push($tagIds, $this->addtagsToDatabase($tag));
            } else {
                $tag = trim(strtolower($tag->name));
                $this->warn("Not adding '$tag' tag");
            }
        }
        return $tagIds;
    }


    /**
     * Fetches artists from LastFM
     */
    protected function fetchArtists(): int
    {
        $totalArtist = $this->artistsPerPage * $this->artistPageLimit;
        $artistFetched = 0;
        $totalRetries = 0;
        // whatever happens first: artist limit reached or an error response
        for ($page = 1; $artistFetched < $totalArtist; $page++) {
            $zeroFilledPageNumber = str_pad((string) $page, 2, "0", STR_PAD_LEFT);
            $this->info("Retrieving page $zeroFilledPageNumber: $artistFetched/$totalArtist artists fetched");
            try {
                $result = $this->getArtistPage($page);
                $pageArtists = $result->artists->artist;
                $this->fetchTracks($pageArtists);

                $this->addArtistsToDatabase($pageArtists);

                $artistCount = count($pageArtists);
                $artistFetched += $artistCount;

                if (!$pageArtists || $artistCount < $this->minArtistPerPage) {
                    // not enough artists to complete page, we'll take it as if
                    // we retrieved all retrievable artists
                    $this->warn("Not enough artists ($artistCount) to fill a page ($this->artistsPerPage). Leaving seeder.");
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
            [
                'limit' => $this->artistsPerPage,
                'page' => $page
            ]
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
            [
                'limit' => $this->tracksPerArtist,
                'artist' => $artist
            ]
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
            [
                'track' => $track,
                'artist' => $artist
            ]
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
        $tagExists = Tag::where('name', trim(strtolower($tag->name)))->exists();
        if (empty($tagExists)) {
            $tag = Tag::create([
                'name' => trim(strtolower($tag->name)),
                'url' => $tag->url
            ]);
            if ($tag->name == "brazilian") {
                $this->error("Look at me, i'm been saved: $tag->name - Id: $tag->id");
            }
            return $tag->id;
        }
        $id = Tag::where('name', trim(strtolower($tag->name)))->value('id');
        if (!isset($id)) {
            $this->error("Unexpected error finding the tag: $tag->name - Id: $id");
            return -1;
        }
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
            try {
                $trackInfo = $this->getTrackInfo($artist, $track->name);
                if (!isset($trackInfo->track)) {
                    $this->error("$artist - $track->name couldn't get added to database");
                    continue;
                }
                $tags = $this->getTagIds($trackInfo);

                $trackInfo = $trackInfo->track;

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
            } catch (Exception $e) {
                $this->error("Not enough tracks fetched: $e");
                return;
            }
        }
    }
}
