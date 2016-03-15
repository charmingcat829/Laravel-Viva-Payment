<?php

use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Transaction;

class TransactionFunctionalTest extends TestCase
{
    // Need a native source for this

    /**
     * @test
     * @group functional
     */
    public function it_creates_a_transaction()
    {
        $order = app(Order::class);
        $transaction = app(Transaction::class);
        $card = app(Card::class);

        $orderCode = $order->create(1, [
            'CustomerTrns' => 'Test Transaction',
            'SourceCode' => 4693,
        ]);

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, 06, 2016);
        $installments = $card->installments('4111 1111 1111 1111');

        $response = $transaction->create([
            'OrderCode'     => $orderCode,
            'SourceCode'    => 4693,
            'Installments'  => $installments,
            'CreditCard'    => [
                'Token'     => $token,
            ],
        ]);

        dd($response);
    }

    /**
     * @test
     * @group functional
     */
    public function it_creates_a_recurring_transaction()
    {
    }

    /**
     * @test
     * @group functional
     */
    public function it_refunds_a_transaction()
    {
    }

    /**
     * @test
     * @group functional
     */
    public function it_gets_transactions_by_id()
    {
    }

    /**
     * @test
     * @group functional
     */
    public function it_gets_transactions_by_order_code()
    {
        $order = app(Order::class);
        $transaction = app(Transaction::class);

        $orderCode = $order->create(1, ['CustomerTrns' => 'Test Transaction']);

        $transactions = $transaction->getByOrder($orderCode);

        $this->assertTrue(is_array($transactions));
    }

    /**
     * @test
     * @group functional
     */
    public function it_gets_transactions_by_date()
    {
        $transaction = app(Transaction::class);

        $transactions = $transaction->getByDate('2016-03-12');

        $this->assertTrue(is_array($transactions));
    }

    /**
     * @test
     * @group functional
     */
    public function it_gets_transactions_by_clearance_date()
    {
        $transaction = app(Transaction::class);

        $transactions = $transaction->getByClearanceDate('2016-03-12');

        $this->assertTrue(is_array($transactions));
    }
}
