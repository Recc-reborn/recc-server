<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Log;

use App\Models\Artist;
use App\Services\LastFMService;

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
    protected $pageLimit = 20;

    /**
     * How many artists per page?
     *
     * @var int
     */
    protected $perPage = 500;

    /**
     * How many artists per page?
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
    public function handle(LastFMService $lastFM)
    {
        $this->lastFM = $lastFM;
        $this->warn("[!IMPORTANT!] This command should NOT be run too often as it is pretty heavy on LastFM's servers and we don't want to get banned!");

        $totalRetries = 0;
        // whatever happens first: artist limit reached or an error response
        for ($page = 1; $page <= $this->pageLimit; $page++) {
            $zeroFilledPageNumber = str_pad((string) $page, 2, "0", STR_PAD_LEFT);
            $this->info("Retrieving page $zeroFilledPageNumber / $this->pageLimit");
            try {
                $result = $this->getPage($page);
                $pageArtists = $result->artists->artist;

                $this->addArtistsToDatabase($pageArtists);

                $artistCount = count($pageArtists);

                if (!$pageArtists || $artistCount < $this->perPage) {
                    // not enough artists to complete page, we'll take it as if
                    // we retrieved all retrievable artists
                    $this->warn("Not enough artists ($paddedCount) to fill a page ($this->perPage). Leaving seeder.");
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
    protected function getPage(int $page)
    {
        return $this->lastFM->call(
            'chart.getTopArtists',
            ['limit' => $this->perPage, 'page' => $page]
        );
    }

    /**
     * Takes artists as they come from LastFM and creates an \App\Models\Artist
     * instance out of each of them.
     *
     * @param mixed[] $artists
     */
    public function addArtistsToDatabase($artists)
    {
        // LastFM responses are weird
        foreach ($artists as $artist) {
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
}
