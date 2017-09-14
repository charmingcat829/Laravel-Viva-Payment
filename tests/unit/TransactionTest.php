<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Illuminate\Support\Carbon;
use DateTime;
use Sebdesign\VivaPayments\Transaction;
use Sebdesign\VivaPayments\Test\TestCase;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_a_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $parameters = [
            'OrderCode'     => 175936509216,
            'SourceCode'    => 'Default',
            'Installments'  => 36,
            'CreditCard'    => [
                'Token'     => 'foo',
            ],
        ];

        $response = $transaction->create($parameters);
        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertQuery('key', $this->app['config']->get('services.viva.public_key'), $request);
        $this->assertBody('OrderCode', 175936509216, $request);
        $this->assertBody('SourceCode', 'Default', $request);
        $this->assertBody('Installments', 36, $request);
        $this->assertBody('CreditCard', ['Token' => 'foo'], $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_creates_a_recurring_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $response = $transaction->createRecurring('252b950e-27f2-4300-ada1-4dedd7c17904', 30, [
            'MerchantTrns' => 'Your reference',
        ]);

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertBody('Amount', 30, $request);
        $this->assertBody('MerchantTrns', 'Your reference', $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_a_transaction()
    {
        $this->mockJsonResponses([['foo' => 'bar']]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $response = $transaction->cancel('252b950e-27f2-4300-ada1-4dedd7c17904', 30, 'Customer name');
        $request = $this->getLastRequest();

        $this->assertMethod('DELETE', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertQuery('Amount', 30, $request);
        $this->assertQuery('ActionUser', 'Customer name', $request);
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_id()
    {
        $this->mockJsonResponses([['Transactions' => []]]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->get('252b950e-27f2-4300-ada1-4dedd7c17904');
        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertPath('/api/transactions/252b950e-27f2-4300-ada1-4dedd7c17904', $request);
        $this->assertInternalType('array', $transactions);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_order_code()
    {
        $this->mockJsonResponses([['Transactions' => []]]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $transactions = $transaction->getByOrder(175936509216);
        $request = $this->getLastRequest();

        $this->assertMethod('GET', $request);
        $this->assertQuery('ordercode', 175936509216, $request);
        $this->assertInternalType('array', $transactions);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_date()
    {
        $this->mockJsonResponses([
            ['Transactions' => []],
            ['Transactions' => []],
            ['Transactions' => []],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $responses = [
            $transaction->getByDate('2016-03-12'),
            $transaction->getByDate(new DateTime('2016-03-12')),
            $transaction->getByDate(new Carbon('2016-03-12')),
        ];

        foreach ($this->requests as $key => $transactions) {
            $request = $transactions['request'];

            $this->assertMethod('GET', $request);
            $this->assertQuery('date', '2016-03-12', $request);
            $this->assertInternalType('array', $responses[$key]);
        }
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_clearance_date()
    {
        $this->mockJsonResponses([
            ['Transactions' => []],
            ['Transactions' => []],
            ['Transactions' => []],
        ]);
        $this->mockRequests();

        $transaction = new Transaction($this->client);

        $responses = [
            $transaction->getByClearanceDate('2016-03-12'),
            $transaction->getByClearanceDate(new DateTime('2016-03-12')),
            $transaction->getByClearanceDate(new Carbon('2016-03-12')),
        ];

        foreach ($this->requests as $key => $transactions) {
            $request = $transactions['request'];

            $this->assertMethod('GET', $request);
            $this->assertQuery('clearancedate', '2016-03-12', $request);
            $this->assertInternalType('array', $responses[$key]);
        }
    }
}
