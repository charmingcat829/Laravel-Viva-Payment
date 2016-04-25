<?php

use Sebdesign\VivaPayments\Card;

class CardTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_creates_a_token()
    {
        $this->mockJsonResponses([['Token' => 'foo']]);
        $this->mockRequests();

        $card = new Card($this->client);

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, 06, 2016);
        $request = $this->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);
        parse_str($request->getBody(), $body);

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $query['key'], 'The public key should be passed as a query.');
        $this->assertTrue(is_string($token));
        $this->assertEquals('Customer name', $body['CardHolderName'], 'The cardholder name should be Customer name.');
        $this->assertEquals(4111111111111111, $body['Number'], 'The card number should be 4111111111111111.');
        $this->assertEquals(111, $body['CVC'], 'The CVC number should be 111.');
        $this->assertEquals('2016-06-15', $body['ExpirationDate'], 'The expiration date should be 2016-06-15.');
        $this->assertEquals('foo', $token, 'The token should be foo');
    }

    /**
     * @test
     * @group unit
     */
    public function it_checks_for_installments()
    {
        $this->mockJsonResponses([['MaxInstallments' => 36]]);
        $this->mockRequests();

        $card = new Card($this->client);

        $installments = $card->installments('4111 1111 1111 1111');
        $request = $this->getLastRequest();

        parse_str($request->getUri()->getQuery(), $query);
        parse_str($request->getBody(), $body);

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $query['key'], 'The public key should be passed as a query.');
        $this->assertTrue($request->hasHeader('CardNumber'), 'The card number should be passed as a header.');
        $this->assertEquals(4111111111111111, $request->getHeader('CardNumber')[0], 'The card number should be 4111111111111111.');
        $this->assertEquals(36, $installments, 'The installments should be 36.');
    }
}
