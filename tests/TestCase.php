<?php

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

abstract class TestCase extends Orchestra\Testbench\TestCase
{
    protected $client;
    protected $handler;
    protected $requests = [];
    protected $responses = [];

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
        $history = Middleware::history($this->requests);

        $this->handler->push($history);
    }

    protected function getLastRequest()
    {
        return $this->requests[0]['request'];
    }

    protected function mockResponses(array $responses)
    {
        $mock = new MockHandler($responses);
        $this->handler = HandlerStack::create($mock);

        $this->makeClient();
    }

    /**
     * Make a client instance from a Guzzle handler.
     *
     * @param   GuzzleHttp\HandlerStack $handler
     */
    protected function makeClient()
    {
        $mockClient = new GuzzleClient([
            'handler' => $this->handler,
            'base_uri' => Client::DEMO_URL,
            'curl' => [
                CURLOPT_SSL_CIPHER_LIST => 'TLSv1',
            ],
            'auth' => [
                $this->app['config']['merchant_id'],
                $this->app['config']['api_key'],
            ],
        ]);

        $this->client = new Client($mockClient);
    }

    protected function mockJsonResponses(array $bodies)
    {
        $responses = array_map(function ($body) {
            return new Response(200, [], json_encode($body));
        }, $bodies);

        $this->mockResponses($responses);
    }
}
