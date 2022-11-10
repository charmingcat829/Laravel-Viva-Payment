<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\ClientException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;

/** @covers \Sebdesign\VivaPayments\Services\Card */
class CardTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_cannot_create_a_card_token_for_a_transaction_that_does_not_exist(): void
    {
        try {
            Viva::cards()->createToken('6cffe5bf-909c-4d69-b6dc-2bef1a6202f7');

            $this->fail();
        } catch (ClientException $e) {
            $this->assertEquals(403, $e->getCode());
        }
    }
}
