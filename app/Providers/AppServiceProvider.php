<?php

namespace App\Providers;

use App\NewsFetcher\Implementations\BBCNewsFetcher;
use App\NewsFetcher\Implementations\NewsApiFetcher;
use App\NewsFetcher\Implementations\NewYorkTimesFetcher;
use App\NewsFetcher\NewsFetcher;
use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->tag([BBCNewsFetcher::class, NewsApiFetcher::class, NewYorkTimesFetcher::class], 'news-fetchers');
        $this->app->singleton(NewsFetcher::class, function (Application $app) {
            return new NewsFetcher($app->tagged('news-fetchers'));
        });
    }

    /**
     * Get the services provided by the provider.
     *
     * @return array<int, string>
     */
    public function provides(): array
    {
        return [NewsFetcher::class];
    }
}
