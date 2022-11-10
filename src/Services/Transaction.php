<?php

namespace Sebdesign\VivaPayments\Services;

use GuzzleHttp\RequestOptions;
use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Requests;
use Sebdesign\VivaPayments\Responses;

class Transaction
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * Retrieve transaction.
     *
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions/paths/~1checkout~1v2~1transactions~1{transactionId}/get
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     */
    public function retrieve(string $transactionId, array $guzzleOptions = []): Responses\Transaction
    {
        /** @phpstan-var TransactionArray */
        $response = $this->client->get(
            $this->client->getApiUrl()->withPath("/checkout/v2/transactions/{$transactionId}"),
            array_merge_recursive(
                $this->client->authenticateWithBearerToken(),
                $guzzleOptions,
            )
        );

        return Responses\Transaction::create($response);
    }

    /**
     * Create a recurring transaction.
     *
     * @see https://developer.vivawallet.com/apis-for-payments/payment-api/#tag/Transactions-(Deprecated)/paths/~1api~1transactions~1{transaction_id}/post
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     */
    public function createRecurring(
        string $transactionId,
        Requests\CreateRecurringTransaction $transaction,
        array $guzzleOptions = []
    ): Responses\RecurringTransaction {
        /** @phpstan-var RecurringTransactionArray */
        $response = $this->client->post(
            $this->client->getUrl()->withPath("/api/transactions/{$transactionId}"),
            array_merge_recursive(
                [RequestOptions::JSON => $transaction],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return Responses\RecurringTransaction::create($response);
    }
}
