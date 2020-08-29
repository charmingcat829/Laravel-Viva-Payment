<?php

namespace Sebdesign\VivaPayments;

class Webhook
{
    const ENDPOINT = '/api/messages/config/token/';

    /**
     * Create Transaction event.
     */
    const CREATE_TRANSACTION = 1796;

    /**
     * Cancel/Refund Transaction event.
     */
    const REFUND_TRANSACTION = 1797;

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
     * Get a webhook authorization code.
     *
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function getAuthorizationCode(array $guzzleOptions = [])
    {
        return $this->client->get(self::ENDPOINT, $guzzleOptions);
    }
}
