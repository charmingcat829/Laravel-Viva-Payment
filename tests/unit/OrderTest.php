<?php

use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Client;

class OrderTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_an_order()
    {
        $this->mockJsonResponses([['OrderCode' => 175936509216]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $parameters = ['CustomerTrns' => 'Your reference'];
        $orderCode = $order->create(30, $parameters);
        $request = $this->getLastRequest();

        parse_str($request->getBody(), $body);

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertArraySubset($parameters, $body, 'The parameters should be passed in the request body.');
        $this->assertTrue(isset($body['Amount']), 'The amount should be passed in the parameters.');
        $this->assertEquals(30, $body['Amount'], 'The amount should be 30.');
        $this->assertEquals(175936509216, $orderCode, 'The order code should be 175936509216');
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_an_order()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $order = new Order($this->client);

        $response = $order->get(175936509216);
        $request = $this->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertStringEndsWith('175936509216', $request->getUri()->getPath(), 'The order code should be in the URL.');
        $this->assertEquals(['foo' => 'bar'], (array) $response, 'The response is not correct.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_updates_an_order()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $parameters = ['Amount' => 50];
        $orderCode = $order->update(175936509216, $parameters);
        $request = $this->getLastRequest();

        parse_str($request->getBody(), $body);

        $this->assertEquals('PATCH', $request->getMethod(), 'The request method should be PATCH.');
        $this->assertStringEndsWith('175936509216', $request->getUri()->getPath(), 'The order code should be in the URL.');
        $this->assertEquals($parameters, $body, 'The parameters should be passed in the request body.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_an_order()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);

        $orderCode = 175936509216;

        $response = $order->cancel($orderCode);
        $request = $this->getLastRequest();

        $this->assertEquals('DELETE', $request->getMethod(), 'The request method should be DELETE.');
        $this->assertStringEndsWith((string) $orderCode, $request->getUri()->getPath(), 'The order code should be in the URL.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_a_checkout_url()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $order = new Order($this->client);
        $url = $order->getCheckoutUrl(175936509216);

        $this->assertEquals(Client::DEMO_URL.'/web/checkout?ref=175936509216', $url);
    }
}
