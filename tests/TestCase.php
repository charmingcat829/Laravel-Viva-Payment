<?php

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use GuzzleHttp\Subscriber\History;
use GuzzleHttp\Subscriber\Mock;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

abstract class TestCase extends Orchestra\Testbench\TestCase
{
    protected function getPackageProviders($app)
    {
        return [VivaPaymentsServiceProvider::class];
    }

    /**
     * Define environment setup.
     *
     * @param  \Illuminate\Foundation\Application  $app
     * @return void
     */
    protected function getEnvironmentSetUp($app)
    {
        $env = __DIR__.'/../.env';

        if (is_readable($env) && is_file($env)) {
            $app->loadEnvironmentFrom('../../../../.env');
            $app->make('Illuminate\Foundation\Bootstrap\DetectEnvironment')->bootstrap($app);
        }

        $app['config']->set('services.viva', [
            'api_key' => env('VIVA_API_KEY', str_random()),
            'merchant_id' => env('VIVA_MERCHANT_ID', str_random()),
            'public_key' => env('VIVA_PUBLIC_KEY', str_random()),
            'environment' => env('VIVA_ENVIRONMENT', 'demo'),
        ]);
    }

    protected function mockRequests()
    {
        $history = new History();
        $this->app->make(Client::class)->getClient()->getEmitter()->attach($history);

        return $history;
    }

    protected function mockResponses(array $responses)
    {
        $mock = new Mock($responses);

        // Add the mock subscriber to the client.
        $this->app->make(Client::class)->getClient()->getEmitter()->attach($mock);
    }

    protected function mockJsonResponses(array $bodies)
    {
        $responses = array_map(function ($body) {
            return new Response(200, [], Stream::factory(json_encode($body)));
        }, $bodies);

        $this->mockResponses($responses);
    }
}
