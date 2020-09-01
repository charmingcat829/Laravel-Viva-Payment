<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Illuminate\Support\Carbon;
use Sebdesign\VivaPayments\NativeCheckout;
use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;

class NativeCheckoutTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_creates_a_charge_token_using_card_details()
    {
        app(OAuth::class)->requestToken();

        $expirationDate = Carbon::parse('next year');

        $response = app(NativeCheckout::class)->chargeToken(
            1000,
            'Customer name',
            '4111 1111 1111 1111',
            111,
            $expirationDate->month,
            $expirationDate->year,
            'https://www.example.com'
        );

        $this->assertTrue(is_object($response));
        $this->assertObjectHasAttribute('chargeToken', $response);
        $this->assertObjectHasAttribute('redirectToACSForm', $response);

        return $response->chargeToken;
    }

    /**
     * @test
     * @group functional
     * @depends it_creates_a_charge_token_using_card_details
     */
    public function it_creates_a_card_token_using_charge_token(string $chargeToken)
    {
        $this->markTestSkipped('Charge tokens fail 3DS authentication in demo environment.');

        app(OAuth::class)->requestToken();

        $cardToken = app(NativeCheckout::class)->cardToken($chargeToken);

        $this->assertTrue(is_string($cardToken));

        return $cardToken;
    }

    /**
     * @test
     * @group functional
     * @depends it_creates_a_card_token_using_charge_token
     */
    public function it_creates_a_charge_token_using_card_token(string $cardToken)
    {
        $this->markTestSkipped('Charge tokens fail 3DS authentication in demo environment.');

        app(OAuth::class)->requestToken();

        $chargeToken = app(NativeCheckout::class)->chargeTokenUsingCardToken($cardToken);

        $this->assertTrue(is_string($chargeToken));
    }

    /**
     * @test
     * @group functional
     * @depends it_creates_a_charge_token_using_card_details
     */
    public function it_creates_a_transaction(string $chargeToken)
    {
        $this->markTestSkipped('Charge tokens fail 3DS authentication in demo environment.');

        app(OAuth::class)->requestToken();

        $transactionId = app(NativeCheckout::class)->createTransaction([
            'amount' => 1000,
            'preauth' => false,
            'sourceCode' => env('VIVA_SOURCE_CODE'),
            'chargeToken' => $chargeToken,
            'installments' => 1,
            'merchantTrns' => 'Merchant transaction reference',
            'customerTrns' => 'Description that the customer sees',
            'currencyCode' => 978,
            'customer' => [
                'email' => 'native@vivawallet.com',
                'phone' => '442037347770',
                'fullname' => 'John Smith',
                'requestLang' => 'en',
                'countryCode' => 'GB',
            ],
        ]);

        $this->assertTrue(is_string($transactionId));
    }

    /**
     * @test
     * @group functional
     * @depends it_creates_a_charge_token_using_card_details
     */
    public function it_captures_a_preauth_transaction(string $chargeToken)
    {
        $this->markTestSkipped('Charge tokens fail 3DS authentication in demo environment.');

        app(OAuth::class)->requestToken();

        $preauthTransactionId = app(NativeCheckout::class)->createTransaction([
            'amount' => 1000,
            'preauth' => true,
            'sourceCode' => env('VIVA_SOURCE_CODE'),
            'chargeToken' => $chargeToken,
            'installments' => 1,
            'merchantTrns' => 'Merchant transaction reference',
            'customerTrns' => 'Description that the customer sees',
            'currencyCode' => 978,
            'customer' => [
                'email' => 'native@vivawallet.com',
                'phone' => '442037347770',
                'fullname' => 'John Smith',
                'requestLang' => 'en',
                'countryCode' => 'GB',
            ],
        ]);

        app(OAuth::class)->requestToken();

        $transactionId = app(NativeCheckout::class)->capturePreAuthTransaction($preauthTransactionId, 1000);

        $this->assertTrue(is_string($transactionId));
    }

    /**
     * @test
     * @group unit
     */
    public function it_checks_for_installments()
    {
        app(OAuth::class)->requestToken();

        $maxInstallments = app(NativeCheckout::class)->installments('4111 1111 1111 1111');

        $this->assertTrue(is_int($maxInstallments));
    }
}
