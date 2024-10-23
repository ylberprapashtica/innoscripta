<?php

namespace App\Providers;

use App\NewsFetcher\BBCNewsFetcher;
use App\NewsFetcher\NewsApiFetcher;
use App\NewsFetcher\NewsFetcher;
use App\NewsFetcher\NewYorkTimesFetcher;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->tag([BBCNewsFetcher::class, NewsApiFetcher::class, NewYorkTimesFetcher::class], 'news-fetchers');
        $this->app->bind(NewsFetcher::class, function (Application $app) {
            return new NewsFetcher($app->tagged('news-fetchers'));
        });
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {

    }
}
