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

        parse_str($request->getUri()->getQuery(), $query);
        parse_str($request->getBody(), $body);

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $query['key'], 'The public key should be passed as a query.');
        $this->assertEquals($parameters, $body, 'The request body should be identical to the parameters.');
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

        $original = '252b950e-27f2-4300-ada1-4dedd7c17904';
        $parameters = [
            'MerchantTrns' => 'Your reference',
        ];

        $response = $transaction->createRecurring($original, 30, $parameters);
        $request = $this->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);
        parse_str($request->getBody(), $body);

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertStringEndsWith($original, $request->getUri()->getPath(), 'The original transaction ID should be in the URL.');
        $this->assertEquals(30, $body['Amount'], 'The request body contain the amount.');
        $this->assertArraySubset($parameters, $body, 'The request body should contain the parameters.');
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

        $original = '252b950e-27f2-4300-ada1-4dedd7c17904';
        $customer = 'Customer name';

        $response = $transaction->cancel($original, 30, $customer);
        $request = $this->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        $this->assertEquals('DELETE', $request->getMethod(), 'The request method should be DELETE.');
        $this->assertStringEndsWith($original, $request->getUri()->getPath(), 'The original transaction ID should be in the URL.');
        $this->assertEquals(30, $query['Amount'], 'The query string should contain the amount.');
        $this->assertEquals($customer, $query['ActionUser'], 'The query string should contain the customer.');
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

        $id = '252b950e-27f2-4300-ada1-4dedd7c17904';

        $transactions = $transaction->get($id);
        $request = $this->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertStringEndsWith($id, $request->getUri()->getPath(), 'The transaction ID should be in the URL.');
        $this->assertTrue(is_array($transactions));
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

        $orderCode = 175936509216;

        $transactions = $transaction->getByOrder($orderCode);
        $request = $this->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($orderCode, $query['ordercode'], 'The order code should be passed as a query.');
        $this->assertTrue(is_array($transactions));
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

            parse_str($request->getUri()->getQuery(), $query);

            $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
            $this->assertEquals('2016-03-12', $query['date'], 'The date should be passed as a query.');
            $this->assertTrue(is_array($responses[$key]));
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

            parse_str($request->getUri()->getQuery(), $query);

            $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
            $this->assertEquals('2016-03-12', $query['clearancedate'], 'The date should be passed as a query.');
            $this->assertTrue(is_array($responses[$key]));
        }
    }
}
