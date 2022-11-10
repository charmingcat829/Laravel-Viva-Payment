<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Services\Card;
use Sebdesign\VivaPayments\Services\Order;
use Sebdesign\VivaPayments\Services\Transaction;
use Sebdesign\VivaPayments\Services\Webhook;
use Sebdesign\VivaPayments\Test\TestCase;

/** @covers \Sebdesign\VivaPayments\Facades\Viva */
class VivaTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_cards_service(): void
    {
        $cards = Viva::cards();

        $this->assertInstanceOf(Card::class, $cards);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_orders_service(): void
    {
        $orders = Viva::orders();

        $this->assertInstanceOf(Order::class, $orders);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_transactions_service(): void
    {
        $transactions = Viva::transactions();

        $this->assertInstanceOf(Transaction::class, $transactions);
    }

    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_webhooks_service(): void
    {
        $webhooks = Viva::webhooks();

        $this->assertInstanceOf(Webhook::class, $webhooks);
    }
}
