<?php

namespace App\Console\Commands;

use App\NewsFetcher\NewsFetcher;
use Illuminate\Console\Command;

class FetchNews extends Command
{

    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:fetch-news {term?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * @param iterable $newsFetchers $newsFetchers
     * @return void
     */
    public function handle(NewsFetcher $newsFetcher): void
    {
        $term = $this->argument('term');
        if (empty($term)) {
            $term = fake()->word();
        }
        $this->info("Fetching All News Articles with term '$term'");
        $results = $newsFetcher->gatherAllPromises($term)->wait();
        foreach ($results as $newsFetcherClass => $result) {
            if ($result === null) {
                $this->line($newsFetcherClass . ' articles stored successfully');
            } else {
                $this->error($newsFetcherClass . ' return with error ' . $result);
            }
        }
    }
}
