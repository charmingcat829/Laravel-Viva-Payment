<?php

namespace Sebdesign\VivaPayments;

use Carbon\Carbon;

class Card
{
    const ENDPOINT = '/api/cards/';

    /**
     * @var \Sebdesign\VivaPayments\Client
     */
    protected $client;

    /**
     * Constructor.
     *
     * @param \Sebdesign\VivaPayments\Client $client
     */
    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    /**
     * Get a token for the credit card.
     *
     * @param  string $name          The cardholder's name
     * @param  string $number        The credit card number
     * @param  int    $cvc           The CVC number
     * @param  int    $month         The expiration month
     * @param  int    $year          The expiration year
     * @param  array  $guzzleOptions Additional options for the Guzzle client
     * @return string
     */
    public function token(
        string $name,
        string $number,
        int $cvc,
        int $month,
        int $year,
        array $guzzleOptions = []
    ): string {
        $parameters = [
            'CardHolderName' => $name,
            'Number' => $this->normalizeNumber($number),
            'CVC' => $cvc,
            'ExpirationDate' => $this->getExpirationDate($month, $year),
        ];

        $response = $this->client->post(self::ENDPOINT, array_merge([
            \GuzzleHttp\RequestOptions::FORM_PARAMS => $parameters,
            \GuzzleHttp\RequestOptions::QUERY => [
                'key'=> $this->client->getKey(),
            ],
        ], $guzzleOptions));

        return $response->Token;
    }

    /**
     * Strip non-numeric characters.
     */
    protected function normalizeNumber(string $cardNumber): string
    {
        return preg_replace('/\D/', '', $cardNumber);
    }

    /**
     * Get the expiration date.
     *
     * @param  int $month
     * @param  int $year
     * @return string
     */
    protected function getExpirationDate(int $month, int $year): string
    {
        return Carbon::createFromDate($year, $month, 15)->toDateString();
    }

    /**
     * Check for installments support.
     */
    public function installments(string $cardNumber, array $guzzleOptions = []): int
    {
        $response = $this->client->get(
            self::ENDPOINT.'/installments',
            array_merge([
                \GuzzleHttp\RequestOptions::HEADERS => [
                    'CardNumber' => $this->normalizeNumber($cardNumber),
                ],
                \GuzzleHttp\RequestOptions::QUERY => [
                    'key' => $this->client->getKey(),
                ],
            ], $guzzleOptions)
        );

        return $response->MaxInstallments;
    }
}
