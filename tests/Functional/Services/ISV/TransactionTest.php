<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/**
 * @covers \Sebdesign\VivaPayments\Client
 * @covers \Sebdesign\VivaPayments\Services\ISV
 * @covers \Sebdesign\VivaPayments\Services\ISV\Transaction
 */
class TransactionTest extends TestCase
{
    /**
     * @test
     * @group functional
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_cannot_retrieve_an_isv_transaction_that_does_not_exist(): void
    {
        Viva::withOAuthCredentials(
            strval(env('VIVA_ISV_CLIENT_ID')),
            strval(env('VIVA_ISV_CLIENT_SECRET')),
        );

        try {
            Viva::isv()->transactions()->retrieve('c90d4902-6245-449f-b2b0-51d99cd09cfe');
            $this->fail();
        } catch (RequestException $e) {
            $this->assertEquals(404, $e->getCode());
        }
    }
}
