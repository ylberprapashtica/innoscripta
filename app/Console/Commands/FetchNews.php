<?php

namespace App\Console\Commands;

use App\NewsFetcher\NewsApiFetcher;
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

    public function handle(NewsApiFetcher $newsApiFetcher): void
    {
        $articles = $newsApiFetcher->fetchNewsAsync();
        $articles->wait();
    }
}
