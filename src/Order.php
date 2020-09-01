<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\Psr7\Uri;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\UriInterface;

class Order
{
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
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post
     *
     * @param  int   $amount        The requested amount in the currency's smallest unit of measurement.
     * @param  array $parameters    Optional parameters
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return int
     */
    public function create(
        int $amount,
        array $parameters = [],
        array $guzzleOptions = []
    ) {
        $parameters = array_merge_recursive(['amount' => $amount], $parameters);

        $response = $this->client->post(
            $this->client->getUrl()->withPath('/api/orders'),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->OrderCode;
    }

    /**
     * Retrieve information about an order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders/post
     *
     * @param  int   $orderCode     The 16-digit orderCode for which you wish to retrieve information.
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function get($orderCode, array $guzzleOptions = [])
    {
        return $this->client->get(
            $this->client->getUrl()->withPath("/api/orders/{$orderCode}"),
            array_merge_recursive(
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Update certain information of an order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/patch
     *
     * @param  int    $orderCode     The 16-digit orderCode for which you requested information.
     * @param  array  $parameters
     * @param  array  $guzzleOptions Additional parameters for the Guzzle client
     * @return null
     */
    public function update(
        $orderCode,
        array $parameters,
        array $guzzleOptions = []
    ) {
        return $this->client->patch(
            $this->client->getUrl()->withPath("/api/orders/{$orderCode}"),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Cancel an order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Payments/paths/~1api~1orders~1{orderCode}/delete
     *
     * @param  int   $orderCode     The 16-digit orderCode for which you requested information.
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function cancel($orderCode, array $guzzleOptions = [])
    {
        return $this->client->delete(
            $this->client->getUrl()->withPath("/api/orders/{$orderCode}"),
            array_merge_recursive(
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Get the checkout URL for an order.
     *
     * @param int $orderCode
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
