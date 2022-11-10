<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services\ISV;

use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Requests\CreatePaymentOrder;
use Sebdesign\VivaPayments\Requests\Customer;
use Sebdesign\VivaPayments\Test\TestCase;

/**
 * @covers \Sebdesign\VivaPayments\Client
 * @covers \Sebdesign\VivaPayments\Services\ISV
 * @covers \Sebdesign\VivaPayments\Services\ISV\Order
 */
class OrderTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @covers \Sebdesign\VivaPayments\Requests\CreatePaymentOrder
     */
    public function it_creates_an_isv_payment_order(): void
    {
        Viva::withOAuthCredentials(
            strval(env('VIVA_ISV_CLIENT_ID')),
            strval(env('VIVA_ISV_CLIENT_SECRET')),
        );

        $orderCode = Viva::isv()->orders()->create(new CreatePaymentOrder(
            amount: 1000,
            customerTrns: 'Test customer description',
            customer: new Customer(
                email: 'test@vivawallet.com',
                fullName: 'John Doe',
                phone: '+30999999999',
                countryCode: 'GB',
                requestLang: 'en-GB',
            ),
            sourceCode: strval(env('VIVA_SOURCE_CODE')),
            merchantTrns: 'Test merchant description',
            isvAmount: 1,
            resellerSourceCode: 'Default',
        ));

        $this->assertIsNumeric($orderCode);
    }
}
