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
     * @param  string $name   The cardholder's name
     * @param  mixed  $number The credit card number
     * @param  int    $cvc    The CVC number
     * @param  int    $month  The expiration month
     * @param  int    $year   The expiration year
     * @return string
     */
    public function token($name, $number, $cvc, $month, $year)
    {
        $token = $this->client->post(self::ENDPOINT, [
            'body' => [
                'CardHolderName'    => $name,
                'Number'            => $this->normalizeNumber($number),
                'CVC'               => $cvc,
                'ExpirationDate'    => $this->getExpirationDate($month, $year),
            ],
            'query' => [
                'key'               => $this->getKey(),
            ],
        ]);

        return $token->Token;
    }

    /**
     * Strip non-numeric characters.
     *
     * @param  mixed $number  The credit card number
     * @return int
     */
    protected function normalizeNumber($number)
    {
        return preg_replace('/\D/', '', $number);
    }

    /**
     * Get the public key as query string.
     *
     * @return string
     */
    protected function getKey()
    {
        return config('services.viva.public_key');
    }

    /**
     * Get the expiration date.
     *
     * @param  int $month
     * @param  int $year
     * @return string
     */
    protected function getExpirationDate($month, $year)
    {
        return Carbon::createFromDate($year, $month, 15)->toDateString();
    }

    /**
     * Check for installments support.
     *
     * @param  mixed $number  The credit card number
     * @return int
     */
    public function installments($number)
    {
        $response = $this->client->get(self::ENDPOINT.'/installments', [
            'headers' => [
                'CardNumber' => $this->normalizeNumber($number),
            ],
            'query' => [
                'key' => $this->getKey(),
            ],
        ]);

        return $response->MaxInstallments;
    }
}
