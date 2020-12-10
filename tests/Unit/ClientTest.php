<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use GuzzleHttp\Psr7\Response;
use InvalidArgumentException;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

class ClientTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_throws_an_exception_when_the_environment_is_invalid()
    {
        $this->expectException(InvalidArgumentException::class);

        app('config')->set('services.viva.environment', '');

        app(Client::class);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_demo_url()
    {
        $url = app(Client::class)->getUrl();

        $this->assertEquals(Client::DEMO_URL, $url, 'The URL should be '.Client::DEMO_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_production_url()
    {
        app('config')->set('services.viva.environment', 'production');

        $url = app(Client::class)->getUrl();

        $this->assertEquals(Client::PRODUCTION_URL, $url, 'The URL should be '.Client::PRODUCTION_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_demo_accounts_url()
    {
        $url = app(Client::class)->getAccountsUrl();

        $this->assertEquals(Client::DEMO_ACCOUNTS_URL, $url, 'The URL should be '.Client::DEMO_ACCOUNTS_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_production_accounts_url()
    {
        app('config')->set('services.viva.environment', 'production');

        $url = app(Client::class)->getAccountsUrl();

        $this->assertEquals(Client::PRODUCTION_ACCOUNTS_URL, $url, 'The URL should be '.Client::PRODUCTION_ACCOUNTS_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_demo_api_url()
    {
        $url = app(Client::class)->getApiUrl();

        $this->assertEquals(Client::DEMO_API_URL, $url, 'The URL should be '.Client::DEMO_API_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_the_production_api_url()
    {
        app('config')->set('services.viva.environment', 'production');

        $url = app(Client::class)->getApiUrl();

        $this->assertEquals(Client::PRODUCTION_API_URL, $url, 'The URL should be '.Client::PRODUCTION_API_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_authenticates_with_basic_auth()
    {
        $basic = app(Client::class)->authenticateWithBasicAuth();

        $this->assertArrayHasKey('auth', $basic);
        $this->assertTrue(is_array($basic['auth']));
        $this->assertCount(2, $basic['auth']);
        $this->assertTrue(is_string($basic['auth'][0]));
        $this->assertEquals(config('services.viva.merchant_id'), $basic['auth'][0]);
        $this->assertTrue(is_string($basic['auth'][1]));
        $this->assertEquals(config('services.viva.api_key'), $basic['auth'][1]);
    }

    /**
     * @test
     * @group unit
     */
    public function it_authenticates_with_public_key()
    {
        $key = app(Client::class)->authenticateWithPublicKey();

        $this->assertEquals([
            'query' => ['key' => config('services.viva.public_key')],
        ], $key);
    }

    /**
     * @test
     * @group unit
     */
    public function it_authenticates_with_bearer_token()
    {
        $bearer = app(Client::class)->withToken('foo')->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => ['Authorization' => 'Bearer foo'],
        ], $bearer);
    }

    /**
     * @test
     * @group unit
     */
    public function it_decodes_a_json_response()
    {
        $json = json_encode([
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ]);

        $this->mockResponses([
            new Response(200, [], $json),
        ]);

        $order = new Order($this->client);

        $response = $order->get(42);

        $this->assertEquals(json_decode($json), $response, 'The JSON response was not decoded.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_throws_an_exception()
    {
        $success = [
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ];

        $failure = [
            'ErrorCode' => 1,
            'ErrorText' => 'Some error occurred.',
        ];

        $this->mockJsonResponses(compact('success', 'failure'));

        $order = new Order($this->client);

        $order->get(42);

        $this->expectException(VivaException::class);

        $order->get(43);
    }
}
