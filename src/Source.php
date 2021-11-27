<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

class Source
{
    const ENDPOINT = '/api/sources/';

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
     * Create a payment source.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Sources/paths/~1api~1sources/post
     *
     * @param  string  $name  A meaningful name that will help you identify the source in Web Self Care environment
     * @param  string  $code  A unique code that is exchanged between your application and the API
     * @param  string  $url  The primary domain of your site WITH protocol scheme (http/https)
     * @param  string  $fail  The relative path url your client will end up to, after a failed transaction
     * @param  string  $success  The relative path url your client will end up to, after a successful transaction
     * @param  array  $guzzleOptions  Additional options for the Guzzle client
     * @return \stdClass
     */
    public function create(
        string $name,
        string $code,
        string $url,
        string $fail,
        string $success,
        array $guzzleOptions = []
    ) {
        $uri = new Uri($url);

        $parameters = [
            'name' => $name,
            'sourceCode' => $code,
            'domain' => $this->getDomain($uri),
            'isSecure' => $this->isSecure($uri),
            'pathFail' => $fail,
            'pathSuccess' => $success,
        ];

        return $this->client->post(
            $this->client->getUrl()->withPath(self::ENDPOINT),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Get the domain of the given URL.
     */
    protected function getDomain(UriInterface $uri): string
    {
        return $uri->getHost();
    }

    /**
     * Check if the given URL has an https:// protocol scheme.
     */
    protected function isSecure(UriInterface $uri): bool
    {
        return strtolower($uri->getScheme()) === 'https';
    }
}
