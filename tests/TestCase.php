<?php

namespace Sebdesign\VivaPayments\Test;

use GuzzleHttp\HandlerStack;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Client as GuzzleClient;
use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\VivaPaymentsServiceProvider;

abstract class TestCase extends \Orchestra\Testbench\TestCase
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
        $app->useEnvironmentPath(__DIR__.'/..');
        $app->make('Illuminate\Foundation\Bootstrap\LoadEnvironmentVariables')->bootstrap($app);
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

    public function assertPath($path, $request)
    {
        $this->assertEquals($path, $request->getUri()->getPath());

        return $this;
    }

    public function assertMethod($name, $request)
    {
        $this->assertEquals($name, $request->getMethod(), "The request method should be [{$name}].");

        return $this;
    }

    public function assertQuery($name, $value, $request)
    {
        $query = $request->getUri()->getQuery();

        parse_str($query, $output);

        $this->assertArrayHasKey(
            $name, $output,
            "Did not see expected query string parameter [{$name}] in [{$query}]."
         );

        $this->assertEquals(
            $value, $output[$name],
            "Query string parameter [{$name}] had value [{$output[$name]}], but expected [{$value}]."
        );

        return $this;
    }

    public function assertBody($name, $value, $request)
    {
        parse_str($request->getBody(), $body);

        $this->assertArrayHasKey($name, $body);

        $this->assertEquals($value, $body[$name]);

        return $this;
    }

    public function assertHeader($name, $value, $request)
    {
        $this->assertTrue($request->hasHeader($name), "The header [{$name}] should be passed as a header.");

        $this->assertEquals($value, $request->getHeader($name)[0], "The header [{$name}] card number should be [{$value}].");

        return $this;
    }
}
