<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\Exception\RequestException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreateRecurringTransaction;
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

    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Requests\CreateRecurringTransaction
     *
     * @throws GuzzleException
     * @throws VivaException
     *
     * @see https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Recurring-Payments/paths/~1api~1transactions~1{id}/post
     */
    public function it_cannot_create_a_recurring_transaction_that_does_not_exist(): void
    {
        $this->expectException(VivaException::class);
        $this->expectExceptionCode(404);

        Viva::withBasicAuthCredentials(
            strval(env('VIVA_ISV_PARTNER_ID')).':'.strval(env('VIVA_MERCHANT_ID')),
            strval(env('VIVA_ISV_PARTNER_API_KEY')),
        );

        Viva::isv()->transactions()->createRecurring(
            '252b950e-27f2-4300-ada1-4dedd7c17904',
            new CreateRecurringTransaction(
                amount: 100,
                isvAmount: 1,
                customerTrns: 'A description of products / services that is displayed to the customer',
                merchantTrns: 'Your merchant reference',
                sourceCode: '6054',
            )
        );
    }
}
