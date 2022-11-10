<?php

namespace Sebdesign\VivaPayments\Services\ISV;

use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Responses;
use Sebdesign\VivaPayments\VivaException;

class Transaction
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * Retrieve transaction.
     *
     * @see https://developer.vivawallet.com/isv-partner-program/payment-isv-api/#tag/Retrieve-Transactions/paths/~1checkout~1v2~1isv~1transactions~1{transactionId}?merchantId={merchantId}/get
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function retrieve(string $transactionId, array $guzzleOptions = []): Responses\Transaction
    {
        /** @phpstan-var TransactionArray */
        $response = $this->client->get(
            $this->client->getApiUrl()->withPath("/checkout/v2/isv/transactions/{$transactionId}"),
            array_merge_recursive(
                [RequestOptions::QUERY => ['merchantId' => $this->client->merchantId]],
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions,
            )
        );

        return Responses\Transaction::create($response);
    }
}
