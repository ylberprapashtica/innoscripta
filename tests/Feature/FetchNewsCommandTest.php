<?php

namespace Tests\Feature;

use App\NewsFetcher\Implementations\BBCNewsFetcher;
use App\NewsFetcher\Implementations\NewsApiFetcher;
use App\NewsFetcher\Implementations\NewYorkTimesFetcher;
use GuzzleHttp\Promise\Promise;
use GuzzleHttp\Psr7\Response as GuzzleResponse;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Storage;
use Mockery;
use Mockery\MockInterface;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

class FetchNewsCommandTest extends TestCase
{
    #[Test]
    public function itCanRunFetchNewsCommand(): void
    {
        $this->instance(
            BBCNewsFetcher::class,
            Mockery::mock(BBCNewsFetcher::class, function (MockInterface $mock) {
                $promise = new Promise(function () use ($mock, &$promise) {
                    $promise->resolve(new Response(new GuzzleResponse(
                        200,
                        [],
                        Storage::disk('test')->get('BBCNewsFetcherResponse.json')
                    )));
                });
                $mock->shouldReceive('makeRequest')->once()->andReturn($promise);
            })->makePartial()
        );

        $this->instance(
            NewsApiFetcher::class,
            Mockery::mock(NewsApiFetcher::class, function (MockInterface $mock) {
                $promise = new Promise(function () use ($mock, &$promise) {
                    $promise->resolve(new Response(new GuzzleResponse(
                        200,
                        [],
                        Storage::disk('test')->get('NewApiFetcherResponse.json')
                    )));
                });
                $mock->shouldReceive('makeRequest')->once()->andReturn($promise);
            })->makePartial()
        );

        $this->instance(
            NewYorkTimesFetcher::class,
            Mockery::mock(NewYorkTimesFetcher::class, function (MockInterface $mock) {
                $promise = new Promise(function () use ($mock, &$promise) {
                    $promise->resolve(new Response(new GuzzleResponse(
                        200,
                        [],
                        Storage::disk('test')->get('NewYorkTimesFetcherResponse.json')
                    )));
                });
                $mock->shouldReceive('makeRequest')->once()->andReturn($promise);
            })->makePartial()
        );

        $this->artisan("app:fetch-news test")->assertSuccessful();
        $this->assertDatabaseCount('articles', 20);
    }
}
