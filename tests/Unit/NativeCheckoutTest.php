<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\NativeCheckout;
use Sebdesign\VivaPayments\Test\TestCase;

class NativeCheckoutTest extends TestCase
{
    /**
     * @test
     */
    public function it_creates_a_charge_token_using_card_details()
    {
        $this->mockJsonResponses([[
            'chargeToken' => 'ctok_bcGN8H87tGi1qtxtEoYkliYgMrc',
            'redirectToACSForm' => '<form>',
        ]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $response = $native->chargeToken(
            1000,
            'John Doe',
            '4111 1111 1111 1111',
            111,
            10,
            2030,
            'https://www.example.com'
        );

        $request = $this->getLastRequest();
        $this->assertMethod('POST', $request);
        $this->assertPath('/nativecheckout/v2/chargetokens', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertJsonBody('amount', 1000, $request);
        $this->assertJsonBody('cvc', 111, $request);
        $this->assertJsonBody('number', '4111111111111111', $request);
        $this->assertJsonBody('holderName', 'John Doe', $request);
        $this->assertJsonBody('expirationYear', 2030, $request);
        $this->assertJsonBody('expirationMonth', 10, $request);
        $this->assertJsonBody('sessionRedirectUrl', 'https://www.example.com', $request);

        $this->assertTrue(is_object($response));
        $this->assertEquals('ctok_bcGN8H87tGi1qtxtEoYkliYgMrc', $response->chargeToken);
        $this->assertEquals('<form>', $response->redirectToACSForm);
    }

    /**
     * @test
     */
    public function it_creates_a_card_token_using_charge_token()
    {
        $this->mockJsonResponses([[
            'token' => '05FB1A1EBF41440FDF88A359C46645B6D1EE3EF5',
        ]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $cardToken = $native->cardToken('ctok__pEfMPKCt-FHnN0vS3T23Gz1aDk');

        $request = $this->getLastRequest();
        $this->assertMethod('GET', $request);
        $this->assertPath('/acquiring/v1/cards/tokens', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertQuery('chargetoken', 'ctok__pEfMPKCt-FHnN0vS3T23Gz1aDk', $request);

        $this->assertTrue(is_string($cardToken));
        $this->assertEquals('05FB1A1EBF41440FDF88A359C46645B6D1EE3EF5', $cardToken);
    }

    /**
     * @test
     * @group unit
     */
    public function it_creates_a_charge_token_using_card_token()
    {
        $this->mockJsonResponses([[
            'token' => 'ctok_17wzXgZFCziELn22JzFm_g_0V74',
        ]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $chargeToken = $native->chargeTokenUsingCardToken('2188A74B6BB8DE0D5671886B5385125121CAE870');

        $request = $this->getLastRequest();
        $this->assertMethod('GET', $request);
        $this->assertPath('/acquiring/v1/cards/tokens', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertQuery('token', '2188A74B6BB8DE0D5671886B5385125121CAE870', $request);

        $this->assertTrue(is_string($chargeToken));
        $this->assertEquals('ctok_17wzXgZFCziELn22JzFm_g_0V74', $chargeToken);
    }

    /**
     * @test
     * @group unit
     */
    public function it_creates_a_transaction()
    {
        $this->mockJsonResponses([[
            'transactionId' => '1549bffb-f82d-4f43-9c07-0818cdcdb2c4',
        ]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $parameters = [
            'amount' => 1000,
            'preauth' => false,
            'sourceCode' => env('VIVA_SOURCE_CODE'),
            'chargeToken' => 'ctok_17wzXgZFCziELn22JzFm_g_0V74',
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
        ];

        $transactionId = $native->createTransaction($parameters);

        $request = $this->getLastRequest();
        $this->assertMethod('POST', $request);
        $this->assertPath('/nativecheckout/v2/transactions', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertJsonBody('amount', 1000, $request);
        $this->assertJsonBody('preauth', false, $request);
        $this->assertJsonBody('sourceCode', env('VIVA_SOURCE_CODE'), $request);
        $this->assertJsonBody('chargeToken', 'ctok_17wzXgZFCziELn22JzFm_g_0V74', $request);
        $this->assertJsonBody('installments', 1, $request);
        $this->assertJsonBody('merchantTrns', 'Merchant transaction reference', $request);
        $this->assertJsonBody('customerTrns', 'Description that the customer sees', $request);
        $this->assertJsonBody('currencyCode', 978, $request);
        $this->assertJsonBody('customer', [
            'email' => 'native@vivawallet.com',
            'phone' => '442037347770',
            'fullname' => 'John Smith',
            'requestLang' => 'en',
            'countryCode' => 'GB',
        ], $request);

        $this->assertTrue(is_string($transactionId));
        $this->assertEquals('1549bffb-f82d-4f43-9c07-0818cdcdb2c4', $transactionId);
    }

    /**
     * @test
     * @group unit
     */
    public function it_captures_a_preauth_transaction()
    {
        $this->mockJsonResponses([[
            'transactionId' => '1549bffb-f82d-4f43-9c07-0818cdcdb2c4',
        ]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $transactionId = $native->capturePreAuthTransaction('b1a3067c-321b-4ec6-bc9d-1778aef2a19d', 300);

        $request = $this->getLastRequest();
        $this->assertMethod('GET', $request);
        $this->assertPath('/nativecheckout/v2/transactions/b1a3067c-321b-4ec6-bc9d-1778aef2a19d', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertJsonBody('amount', 300, $request);

        $this->assertTrue(is_string($transactionId));
        $this->assertEquals('1549bffb-f82d-4f43-9c07-0818cdcdb2c4', $transactionId);
    }

    /**
     * @test
     * @group unit
     */
    public function it_checks_for_installments()
    {
        $this->mockJsonResponses([['maxInstallments' => 3]]);
        $this->mockRequests();

        $native = new NativeCheckout($this->client);

        $this->client->withToken('foo');

        $maxInstallments = $native->installments('4111 1111 1111 1111');

        $request = $this->getLastRequest();
        $this->assertMethod('GET', $request);
        $this->assertPath('/nativecheckout/v2/installments', $request);
        $this->assertHeader('Authorization', 'Bearer foo', $request);
        $this->assertHeader('cardNumber', '4111111111111111', $request);

        $this->assertTrue(is_int($maxInstallments));
        $this->assertEquals(3, $maxInstallments);
    }
}
