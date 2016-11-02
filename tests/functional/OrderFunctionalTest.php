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

        $orderCode = $order->create(1500, [
            'CustomerTrns' => 'Test Transaction',
            'SourceCode' => env('VIVA_SOURCE_CODE'),
            'AllowRecurring' => true,
        ]);

        $this->assertTrue(is_int($orderCode));

        // GET

        $response = $order->get($orderCode);

        $this->assertEquals(Order::PENDING, $response->StateId);
        $this->assertEquals(15, $response->RequestAmount);
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
