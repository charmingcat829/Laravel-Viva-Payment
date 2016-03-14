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
        $card = app(Card::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['Token' => 'foo']]);

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, 06, 2016);
        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $request->getQuery()->get('key'), 'The public key should be passed as a query.');
        $this->assertTrue(is_string($token));
        $this->assertEquals('Customer name', $request->getBody()->getField('CardHolderName'), 'The cardholder name should be Customer name.');
        $this->assertEquals(4111111111111111, $request->getBody()->getField('Number'), 'The card number should be 4111111111111111.');
        $this->assertEquals(111, $request->getBody()->getField('CVC'), 'The CVC number should be 111.');
        $this->assertEquals('2016-06-15', $request->getBody()->getField('ExpirationDate'), 'The expiration date should be 2016-06-15.');
        $this->assertEquals('foo', $token, 'The token should be foo');
    }

    /**
     * @test
     * @group unit
     */
    public function it_checks_for_installments()
    {
        $card = app(Card::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([['MaxInstallments' => 36]]);
        
        $installments = $card->installments('4111 1111 1111 1111');
        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($this->app['config']->get('services.viva.public_key'), $request->getQuery()->get('key'), 'The public key should be passed as a query.');
        $this->assertTrue($request->hasHeader('CardNumber'), 'The card number should be passed as a header.');
        $this->assertEquals(4111111111111111, $request->getHeader('CardNumber'), 'The card number should be 4111111111111111.');
        $this->assertEquals(36, $installments, 'The installments should be 36.');
    }
}