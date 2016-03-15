<?php

use Sebdesign\VivaPayments\Order;

class OrderTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_an_order()
    {
        $order = app(Order::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['OrderCode' => 175936509216]]);

        $parameters = ['CustomerTrns' => 'Your reference'];
        $orderCode = $order->create(30, $parameters);
        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertArraySubset($parameters, $request->getBody()->getFields(), 'The parameters should be passed in the request body.');
        $this->assertTrue($request->getBody()->hasField('Amount'), 'The amount should be passed in the parameters.');
        $this->assertEquals(30, $request->getBody()->getField('Amount'), 'The amount should be 1.');
        $this->assertEquals(175936509216, $orderCode, 'The order code should be 175936509216');
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_an_order()
    {
        $order = app(Order::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['foo' => 'bar']]);

        $response = $order->get(175936509216);
        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertStringEndsWith('175936509216', $request->getUrl(), 'The order code should be in the URL.');
        $this->assertEquals(['foo' => 'bar'], (array) $response, 'The response is not correct.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_updates_an_order()
    {
        $order = app(Order::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([[]]);

        $parameters = ['Amount' => 50];
        $orderCode = $order->update(175936509216, $parameters);
        $request = $history->getLastRequest();

        $this->assertEquals('PATCH', $request->getMethod(), 'The request method should be PATCH.');
        $this->assertStringEndsWith('175936509216', $request->getUrl(), 'The order code should be in the URL.');
        $this->assertEquals($parameters, $request->getBody()->getFields(), 'The parameters should be passed in the request body.');
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_an_order()
    {
        $order = app(Order::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([[]]);

        $orderCode = 175936509216;

        $response = $order->cancel($orderCode);
        $request = $history->getLastRequest();

        $this->assertEquals('DELETE', $request->getMethod(), 'The request method should be DELETE.');
        $this->assertStringEndsWith((string) $orderCode, $request->getUrl(), 'The order code should be in the URL.');
    }
}
