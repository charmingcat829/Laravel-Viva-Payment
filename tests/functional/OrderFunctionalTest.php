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

        $orderCode = $order->create(1, ['CustomerTrns' => 'Test Transaction']);

        $this->assertTrue(is_int($orderCode));

        // GET

        $response = $order->get($orderCode);

        $this->assertEquals(Order::PENDING, $response->StateId);
        $this->assertEquals(0.01, $response->RequestAmount);
        $this->assertEquals('Test Transaction', $response->CustomerTrns);

        // PATCH

        $order->update($orderCode, ['Amount' => 2]);
        $response = $order->get($orderCode);

        $this->assertEquals(0.02, $response->RequestAmount);

        // DELETE

        $order->cancel($orderCode);
        $response = $order->get($orderCode);

        $this->assertEquals(Order::CANCELED, $response->StateId);
    }
}
