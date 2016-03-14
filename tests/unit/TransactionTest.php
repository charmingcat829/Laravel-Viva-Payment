<?php

use Carbon\Carbon;
use Sebdesign\VivaPayments\Transaction;

class TransactionTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_a_transaction()
    {
        $transaction = app(Transaction::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['foo' => 'bar']]);
        
        $parameters = [
            'OrderCode'     => 175936509216,
            'SourceCode'    => 'Default',
            'Installments'  => 36,
            'CreditCard'    => [
                'Token'     => 'foo',
            ],
        ];

        $response = $transaction->create($parameters);
        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $request->getQuery()->get('key'), 'The public key should be passed as a query.');
        $this->assertEquals($parameters, $request->getBody()->getFields(), 'The request body should be identical to the parameters.');
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_creates_a_recurring_transaction()
    {
        $transaction = app(Transaction::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['foo' => 'bar']]);

        $original = '252b950e-27f2-4300-ada1-4dedd7c17904';
        $parameters = [
            'MerchantTrns' => 'Your reference',
        ];

        $response = $transaction->createRecurring($original, 1, $parameters);
        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertStringEndsWith($original, $request->getUrl(), 'The original transaction ID should be in the URL.');
        $this->assertArraySubset($parameters, $request->getBody()->getFields(), 'The request body should contain the parameters.');
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_cancels_a_transaction()
    {
        $transaction = app(Transaction::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['foo' => 'bar']]);

        $original = '252b950e-27f2-4300-ada1-4dedd7c17904';
        $customer = 'Customer name';

        $response = $transaction->cancel($original, 1, $customer);
        $request = $history->getLastRequest();

        $this->assertEquals('DELETE', $request->getMethod(), 'The request method should be DELETE.');
        $this->assertStringEndsWith($original, $request->getUrl(), 'The original transaction ID should be in the URL.');
        $this->assertEquals(1, $request->getBody()->getField('Amount'), 'The request body should contain the amount.');
        $this->assertEquals($customer, $request->getBody()->getField('ActionUser'), 'The request body should contain the customer.');
        $this->assertEquals(['foo' => 'bar'], (array) $response);
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_id()
    {
        $transaction = app(Transaction::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['Transactions' => []]]);

        $id = '252b950e-27f2-4300-ada1-4dedd7c17904';
        
        $transactions = $transaction->get($id);
        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertStringEndsWith($id, $request->getUrl(), 'The transaction ID should be in the URL.');
        $this->assertTrue(is_array($transactions));
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_order_code()
    {
        $transaction = app(Transaction::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['Transactions' => []]]);

        $orderCode = 175936509216;
        
        $transactions = $transaction->getByOrder($orderCode);
        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($orderCode, $request->getQuery()->get('ordercode'), 'The order code should be passed as a query.');
        $this->assertTrue(is_array($transactions));
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_date()
    {
        $transaction = app(Transaction::class);
        
        $history = $this->mockRequests();
        $this->mockJsonResponses([
            ['Transactions' => []],
            ['Transactions' => []],
            ['Transactions' => []],
        ]);

        $responses = [
            $transaction->getByDate('2016-03-12'),
            $transaction->getByDate(new DateTime('2016-03-12')),
            $transaction->getByDate(new Carbon('2016-03-12')),
        ];

        foreach ($history->getRequests() as $key => $request) {
            $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
            $this->assertEquals('2016-03-12', $request->getQuery()->get('date'), 'The date should be passed as a query.');
            $this->assertTrue(is_array($responses[$key]));
        }
    }

    /**
     * @test
     * @group unit
     */
    public function it_gets_transactions_by_clearance_date()
    {
        $transaction = app(Transaction::class);
        
        $history = $this->mockRequests();
        $this->mockJsonResponses([
            ['Transactions' => []],
            ['Transactions' => []],
            ['Transactions' => []],
        ]);

        $responses = [
            $transaction->getByClearanceDate('2016-03-12'),
            $transaction->getByClearanceDate(new DateTime('2016-03-12')),
            $transaction->getByClearanceDate(new Carbon('2016-03-12')),
        ];

        foreach ($history->getRequests() as $key => $request) {
            $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
            $this->assertEquals('2016-03-12', $request->getQuery()->get('clearancedate'), 'The date should be passed as a query.');
            $this->assertTrue(is_array($responses[$key]));
        }
    }
}