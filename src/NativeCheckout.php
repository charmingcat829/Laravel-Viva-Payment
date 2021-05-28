<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\RequestOptions;

class NativeCheckout
{
    /**
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Generate one-time charge token using card details.
     *
     * @link https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-1-generate-one-time-charge-token-using-card-details
     */
    public function chargeToken(
        int $amount,
        string $name,
        string $cardNumber,
        int $cvc,
        int $month,
        int $year,
        string $url,
        array $guzzleOptions = []
    ): \stdClass {
        $parameters = [
            'amount' => $amount,
            'cvc' => $cvc,
            'number' => $this->normalizeNumber($cardNumber),
            'holderName' => $name,
            'expirationYear' => $year,
            'expirationMonth' => $month,
            'sessionRedirectUrl' => $url,
        ];

        return $this->client->post(
            $this->client->getApiUrl()->withPath('/nativecheckout/v2/chargetokens'),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );
    }

    /**
     * Generate card token using the charge token.
     *
     * You can save the card token for future transactions by using the following call.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-2-generate-card-token-using-the-charge-token-optional
     */
    public function cardToken(string $chargeToken, array $guzzleOptions = []): string
    {
        $parameters = ['chargetoken' => $chargeToken];

        $response = $this->client->get(
            $this->client->getApiUrl()->withPath('/acquiring/v1/cards/tokens'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        return $response->token;
    }

    /**
     * Generate charge token using the card token.
     *
     * Each time you want to charge a card you would generate a new charge token from the card token.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/card-tokenization-api/#step-3-generate-one-time-charge-token-using-card-token-optional
     */
    public function chargeTokenUsingCardToken(string $cardToken, array $guzzleOptions = []): string
    {
        $parameters = ['token' => $cardToken];

        $response = $this->client->get(
            $this->client->getApiUrl()->withPath('/acquiring/v1/cards/chargetokens'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        return $response->chargeToken;
    }

    /**
     * Create transaction.
     *
     * After a successful call to chargetokens, you need to create a new transaction.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#create-transaction
     */
    public function createTransaction(array $parameters, array $guzzleOptions = []): string
    {
        $response = $this->client->post(
            $this->client->getApiUrl()->withPath(
                '/nativecheckout/v2/transactions'
            ),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        return $response->transactionId;
    }

    /**
     * Capture a pre-auth.
     *
     * For cases where a pre-authorization is created instead of a charge,
     * a separate call to the transaction endpoint will be required.
     * Pass the amount of the pre-authorization, or a smaller amount, to create the charge.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#capture-a-pre-auth
     */
    public function capturePreAuthTransaction(
        string $preauthTransactionId,
        int $amount,
        array $guzzleOptions = []
    ): string {
        $parameters = ['amount' => $amount];

        $response = $this->client->post(
            $this->client->getApiUrl()->withPath(
                "/nativecheckout/v2/transactions/{$preauthTransactionId}"
            ),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        return $response->transactionId;
    }

    /**
     * Check for installments.
     *
     * Pass the number as an HTTP header to retrieve the maximum number of installments allowed on a card.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/native-checkout-v2-api/#check-for-installments
     */
    public function installments(string $cardNumber, array $guzzleOptions = []): int
    {
        $parameters = ['cardNumber' => $this->normalizeNumber($cardNumber)];

        $response = $this->client->get(
            $this->client->getApiUrl()->withPath('/nativecheckout/v2/installments'),
            array_merge_recursive(
                [RequestOptions::HEADERS => $parameters],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions
            )
        );

        return $response->maxInstallments;
    }

    /**
     * Strip non-numeric characters.
     */
    protected function normalizeNumber(string $cardNumber): string
    {
        return preg_replace('/\D/', '', $cardNumber);
    }
}
