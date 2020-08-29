<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Client as GuzzleClient;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\UriInterface;

class Client
{
    /**
     * Demo environment URL.
     */
    const DEMO_URL = 'https://demo.vivapayments.com';

    /**
     * Production environment URL.
     */
    const PRODUCTION_URL = 'https://www.vivapayments.com';

    /**
     * @var \GuzzleHttp\Client
     */
    protected $client;

    /**
     * Constructor.
     */
    public function __construct(GuzzleClient $client)
    {
        $this->client = $client;
    }

    /**
     * Make a GET request.
     *
     * @param  string $url
     * @param  array  $options
     * @return \stdClass
     */
    public function get(string $url, array $options = [])
    {
        $response = $this->client->get($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a POST request.
     *
     * @param  string $url
     * @param  array  $options
     * @return \stdClass
     */
    public function post(string $url, array $options = [])
    {
        $response = $this->client->post($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a PATCH request.
     *
     * @param  string $url
     * @param  array  $options
     * @return \stdClass|null
     */
    public function patch(string $url, array $options = [])
    {
        $response = $this->client->patch($url, $options);

        return $this->getBody($response);
    }

    /**
     * Make a DELETE request.
     *
     * @param  string $url
     * @param  array  $options
     * @return \stdClass
     */
    public function delete(string $url, array $options = [])
    {
        $response = $this->client->delete($url, $options);

        return $this->getBody($response);
    }

    /**
     * Get the response body.
     *
     * @param  \Psr\Http\Message\ResponseInterface $response
     * @return \stdClass|null
     *
     * @throws \Sebdesign\VivaPayments\VivaException
     */
    protected function getBody(ResponseInterface $response)
    {
        /** @var \stdClass|null $body */
        $body = json_decode($response->getBody(), false, 512, JSON_BIGINT_AS_STRING);

        if (isset($body->ErrorCode) && $body->ErrorCode !== 0) {
            throw new VivaException($body->ErrorText, $body->ErrorCode);
        }

        return $body;
    }

    /**
     * Get the URL.
     */
    public function getUrl(): UriInterface
    {
        return $this->client->getConfig('base_uri');
    }

    /**
     * Get the Guzzle client.
     */
    public function getClient(): GuzzleClient
    {
        return $this->client;
    }

    /**
     * Get the public key as query string.
     */
    public function getKey(): string
    {
        return config('services.viva.public_key');
    }
}
