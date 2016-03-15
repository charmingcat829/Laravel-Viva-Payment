<?php

use Sebdesign\VivaPayments\Order;

class OrderFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function api_methods()
    {
        $order = app(Order::class);

        // POST

        $orderCode = $order->create(30, ['CustomerTrns' => 'Test Transaction']);

        $this->assertTrue(is_int($orderCode));

        // GET

        $response = $order->get($orderCode);

        $this->assertEquals(Order::PENDING, $response->StateId);
        $this->assertEquals(0.3, $response->RequestAmount);
        $this->assertEquals('Test Transaction', $response->CustomerTrns);

        // PATCH

        $order->update($orderCode, ['Amount' => 50]);
        $response = $order->get($orderCode);

        $this->assertEquals(0.5, $response->RequestAmount);

        // DELETE

        $order->cancel($orderCode);
        $response = $order->get($orderCode);

        $this->assertEquals(Order::CANCELED, $response->StateId);
    }
}
