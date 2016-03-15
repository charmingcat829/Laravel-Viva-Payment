<?php

use GuzzleHttp\Message\Response;
use GuzzleHttp\Stream\Stream;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\VivaException;

class ClientTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_gets_the_url()
    {
        $client = app(Client::class);

        $this->assertEquals(Client::DEMO_URL, $client->getUrl(), 'The URL should be '.Client::DEMO_URL);
    }

    /**
     * @test
     * @group unit
     */
    public function it_decodes_a_json_response()
    {
        $order = app(Order::class);

        $json = json_encode([
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ]);

        $this->mockResponses([
            new Response(200, [], Stream::factory($json)),
        ]);

        $response = $order->get('foo');

        $this->assertEquals(json_decode($json), $response, 'The JSON response was not decoded.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_throws_an_exception()
    {
        $order = app(Order::class);

        $success = [
            'ErrorCode' => 0,
            'ErrorText' => 'No errors.',
        ];

        $failure = [
            'ErrorCode' => 1,
            'ErrorText' => 'Some error occurred.',
        ];

        $this->mockJsonResponses(compact('success', 'failure'));

        $order->get('foo');

        $this->setExpectedException(VivaException::class);

        $order->get('bar');
    }
}
