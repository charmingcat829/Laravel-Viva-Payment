<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\RequestOptions;

class OAuth
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
     * Request access token and set it to the client.
     *
     * @param  string|null $clientId
     * @param  string|null $clientSecret
     * @param  array       $guzzleOptions
     * @return \stdClass
     */
    public function requestToken(
        ?string $clientId = null,
        ?string $clientSecret = null,
        array $guzzleOptions = []
    ) {
        $response = $this->token(
            $clientId ?? config('services.viva.client_id'),
            $clientSecret ?? config('services.viva.client_secret'),
            $guzzleOptions
        );

        $this->withToken($response->access_token);

        return $response;
    }

    /**
     * Set the given token to the client.
     */
    public function withToken(string $token): self
    {
        $this->client->withToken($token);

        return $this;
    }

    /**
     * Request access token.
     *
     * @param  string $clientId
     * @param  string $clientSecret
     * @param  array  $guzzleOptions Additional options for the Guzzle client
     * @return \stdClass
     */
    public function token(
        string $clientId,
        string $clientSecret,
        array $guzzleOptions = []
    ) {
        $parameters = ['grant_type' => 'client_credentials'];

        return $this->client->post(
            $this->client->getAccountsUrl()->withPath('/connect/token'),
            array_merge([
                RequestOptions::FORM_PARAMS => $parameters,
                RequestOptions::AUTH => [$clientId, $clientSecret],
            ], $guzzleOptions)
        );
    }
}
