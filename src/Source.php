<?php

namespace Sebdesign\VivaPayments;

class Source
{
    const ENDPOINT = '/api/sources/';

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
     * Create a payment source.
     *
     * @param  string $name    A meaningful name that will help you identify the source in Web Self Care environment
     * @param  string $code    A unique code that is exchanged between your application and the API
     * @param  string $url     The primary domain of your site WITH protocol scheme (http/https)
     * @param  string $fail    The relative path url your client will end up to, after a failed transaction
     * @param  string $success The relative path url your client will end up to, after a successful transaction
     * @return object
     */
    public function create($name, $code, $url, $fail, $success)
    {
        return $this->client->post(self::ENDPOINT, [
            \GuzzleHttp\RequestOptions::FORM_PARAMS => [
                'Name'          => $name,
                'SourceCode'    => $code,
                'Domain'        => $this->getDomain($url),
                'isSecure'      => $this->isSecure($url),
                'PathFail'      => $fail,
                'PathSuccess'   => $success,
            ],
        ]);
    }

    /**
     * Get the domain of the given URL.
     *
     * @param  string $url
     * @return string
     */
    protected function getDomain($url)
    {
        return parse_url($url, PHP_URL_HOST);
    }

    /**
     * Check if the given URL has an https:// protocol scheme.
     *
     * @param  string  $url
     * @return bool
     */
    protected function isSecure($url)
    {
        $scheme = parse_url($url, PHP_URL_SCHEME);

        return strtolower($scheme) === 'https';
    }
}
