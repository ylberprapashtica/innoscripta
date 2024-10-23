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
    protected $signature = 'app:fetch-news';

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
        $newsFetcher->fetchNews();
    }
}
