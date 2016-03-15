<?php

use Carbon\Carbon;
use Sebdesign\VivaPayments\Card;
use Sebdesign\VivaPayments\Order;
use Sebdesign\VivaPayments\Transaction;

class TransactionFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function api_methods()
    {
        $order = app(Order::class);
        $transaction = app(Transaction::class);
        $card = app(Card::class);

        $orderCode = $order->create(30, [
            'CustomerTrns' => 'Test Transaction',
            'SourceCode' => 4693,
            'AllowRecurring' => true,
        ]);

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, 06, 2016);
        $installments = $card->installments('4111 1111 1111 1111');

        // Create transaction

        $original = $transaction->create([
            'OrderCode'     => $orderCode,
            'SourceCode'    => 4693,
            'Installments'  => $installments,
            'CreditCard'    => [
                'Token'     => $token,
            ],
        ]);

        $this->assertEquals(Transaction::COMPLETED, $original->StatusId, 'The transaction was not completed.');
        $this->assertEquals(0.3, $original->Amount);

        // Create recurring transaction

        // $recurring = $transaction->createRecurring($original->TransactionId, [
        //     'Amount'        => 50,
        //     'SourceCode'    => 4693,
        //     'Installments'  => $installments,
        // ]);

        // dump($recurring);

        // $this->assertEquals(Transaction::COMPLETED, $recurring->StatusId, 'The transaction was not completed.');
        // $this->assertEquals(0., $recurring->Amount);

        // Get by ID

        $transactions = $transaction->get($original->TransactionId);

        $this->assertNotEmpty($transactions);
        $this->assertCount(1, $transactions, 'There should be 1 transaction.');
        $this->assertEquals(Transaction::COMPLETED, $transactions[0]->StatusId, 'The transaction was not completed.');
        $this->assertEquals($original->TransactionId, $transactions[0]->TransactionId, "The transaction ID should be {$original->TransactionId}.");

        // Get by order code

        $transactions = $transaction->getByOrder($orderCode);

        $this->assertNotEmpty($transactions);

        foreach ($transactions as $key => $trns) {
            $this->assertEquals($orderCode, $trns->Order->OrderCode, "Transaction #{$key} should have order code {$orderCode}");
        }

        // Get by date

        $transactions = $transaction->getByDate(Carbon::today());

        $this->assertNotEmpty($transactions);

        foreach ($transactions as $key => $trns) {
            $this->assertTrue(Carbon::parse($trns->InsDate)->isToday());
        }

        // Get by clearance date

        $transactions = $transaction->getByClearanceDate(Carbon::today());

        foreach ($transactions as $key => $trns) {
            $this->assertTrue(Carbon::parse($trns->ClearanceDate)->isToday());
        }

        // Cancel transaction

        $response = $transaction->cancel($original->TransactionId, 30);

        $this->assertEquals(Transaction::COMPLETED, $response->StatusId, 'The cancel transaction was not completed.');
        $this->assertEquals(0.3, $response->Amount);

        $transactions = $transaction->get($original->TransactionId);

        $this->assertNotEmpty($transactions);
        $this->assertCount(1, $transactions, 'There should be 1 transaction.');
        $this->assertEquals(Transaction::CANCELED, $transactions[0]->StatusId, 'The original transaction should be canceled.');
        $this->assertEquals(0.3, $transactions[0]->Amount);
    }
}
