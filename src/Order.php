<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;
use Psr\Http\Message\UriInterface;

class Order
{
    const ENDPOINT = '/api/orders/';

    const PENDING = 0;
    const EXPIRED = 1;
    const CANCELED = 2;
    const PAID = 3;

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
     * Create a payment order.
     *
     * @param  int   $amount        Amount in cents
     * @param  array $parameters    Optional parameters (Full list available here: https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post)
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return int
     */
    public function create(
        int $amount,
        array $parameters = [],
        array $guzzleOptions = []
    ) {
        $parameters = array_merge(['amount' => $amount], $parameters);

        $response = $this->client->post(self::ENDPOINT, array_merge([
            \GuzzleHttp\RequestOptions::JSON => $parameters,
        ], $guzzleOptions));

        return $response->OrderCode;
    }

    /**
     * Retrieve information about an order.
     *
     * @param  int   $orderCode     The unique Payment Order ID.
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function get($orderCode, array $guzzleOptions = [])
    {
        return $this->client->get(self::ENDPOINT.$orderCode, $guzzleOptions);
    }

    /**
     * Update certain information of an order.
     *
     * @param  int    $orderCode     The unique Payment Order ID.
     * @param  array  $parameters
     * @param  array  $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function update(
        $orderCode,
        array $parameters,
        array $guzzleOptions = []
    ) {
        return $this->client->patch(self::ENDPOINT.$orderCode, array_merge([
            \GuzzleHttp\RequestOptions::JSON => $parameters,
        ], $guzzleOptions));
    }

    /**
     * Cancel an order.
     *
     * @param  int   $orderCode     The unique Payment Order ID.
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function cancel($orderCode, array $guzzleOptions = [])
    {
        return $this->client->delete(self::ENDPOINT.$orderCode, $guzzleOptions);
    }

    /**
     * Get the checkout URL for an order.
     */
    public function getCheckoutUrl($orderCode): UriInterface
    {
        return Uri::withQueryValue(
            $this->client->getUrl()->withPath('web/checkout'),
            'ref',
            (string) $orderCode
        );
    }
}
