<?php

use Carbon\Carbon;
use Sebdesign\VivaPayments\Card;

class CardFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_creates_a_token()
    {
        // arrange

        $card = app(Card::class);
        $expirationDate = Carbon::parse('next year');

        // act

        $token = $card->token('Customer name', '4111 1111 1111 1111', 111, $expirationDate->month, $expirationDate->year);

        // assert

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
