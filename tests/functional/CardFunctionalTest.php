<?php

use Sebdesign\VivaPayments\Card;

class CardFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_creates_a_token()
    {
        $card = app(Card::class);

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, 06, 2016);

        $this->assertTrue(is_string($token));
    }

    /**
     * @test
     * @group functional
     */
    public function it_checks_for_installments()
    {
        $card = app(Card::class);

        $installments = $card->installments('4111 1111 1111 1111');

        $this->assertTrue(is_int($installments));
    }
}
