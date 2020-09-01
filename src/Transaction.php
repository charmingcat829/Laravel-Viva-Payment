<?php

namespace Sebdesign\VivaPayments;

use GuzzleHttp\RequestOptions;

class Transaction
{
    /**
     * Transaction types.
     */

    // A Capture event of a preAuthorized transaction
    const CAPTURE_FROM_PREAUTH = 0;

    // Authorization hold
    const PREAUTH = 1;

    // Refund transaction
    const REFUND_CARD = 4;

    // Card payment transaction
    const CHARGE_CARD = 5;

    // A card payment that will be done with installments
    const CHARGE_CARD_WITH_INSTALLMENTS = 6;

    // A payment cancelation
    const VOID = 7;

    // A Wallet Payment
    const WALLET_CHARGE = 9;

    // A Refund of a Wallet Payment
    const WALLET_REFUND = 11;

    // Refund transaction for a claimed transaction
    const CLAIM_REFUND = 13;

    // Payment made through the DIAS system
    const DIAS_PAYMENT = 15;

    // Cash Payments, through the Viva Payments Authorised Resellers Network
    const CASH_PAYMENT = 16;

    // A Refunded installment
    const REFUND_INSTALLMENTS = 18;

    // Clearance of a transactions batch
    const CLEARANCE = 19;

    // Bank Transfer command from the merchant's wallet to their IBAN
    const BANK_TRANSFER = 24;

    /**
     * Transaction statuses.
     */

    // The transaction was not completed because of an error
    const ERROR = 'E';

    // The transaction is in progress
    const PROGRESS = 'A';

    // The cardholder has disputed the transaction with the issuing Bank
    const DISPUTED = 'M';

    // Dispute Awaiting Response
    const DISPUTE_AWAITING = 'MA';

    // Dispute in Progress
    const DISPUTE_IN_PROGRESS = 'MI';

    // A disputed transaction has been refunded (Dispute Lost)
    const DISPUTE_REFUNDED = 'ML';

    // Dispute Won
    const DISPUTE_WON = 'MW';

    // Suspected Dispute
    const DISPUTE_SUSPECTED = 'MS';

    // The transaction was cancelled by the merchant
    const CANCELED = 'X';

    // The transaction has been fully or partially refunded
    const REFUNDED = 'R';

    // The transaction has been completed successfully
    const COMPLETED = 'F';

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
     * Create a new transaction.
     *
     * @see https://developer.vivawallet.com/online-checkouts/simple-checkout/#step-2-make-the-charge
     *
     * @param  array  $parameters
     * @param  array  $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function create(array $parameters, array $guzzleOptions = [])
    {
        return $this->client->post(
            $this->client->getUrl()->withPath('/api/transactions'),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Create a recurring transaction.
     *
     * This API call allows you to make a new payment by either committing
     * an already authorized transaction or by making a recurring payment.
     * The latter is only permitted if the following two conditions are met:
     *
     * - The cardholder has already been charged successfully in the past
     * - The cardholder has agreed to allow recurring payments on their card
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/post
     *
     * @param  string   $id            The transaction's unique ID
     * @param  int      $amount        The amount requested in the currency's smallest unit of measurement
     * @param  array    $parameters    Transaction parameters
     * @param  array    $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function createRecurring(
        string $id,
        int $amount,
        array $parameters = [],
        array $guzzleOptions = []
    ) {
        $parameters = array_merge(['amount' => $amount], $parameters);

        return $this->client->post(
            $this->client->getUrl()->withPath("/api/transactions/{$id}"),
            array_merge_recursive(
                [RequestOptions::JSON => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Get details for a single transaction, identified by its transactionId.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get
     *
     * @param  string $id
     * @param  array  $guzzleOptions Additional parameters for the Guzzle client
     * @return array
     */
    public function get(string $id, array $guzzleOptions = []): array
    {
        $response = $this->client->get(
            $this->client->getUrl()->withPath("/api/transactions/{$id}"),
            array_merge_recursive(
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->Transactions;
    }

    /**
     * Get details for all transactions for a given payment order.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get
     *
     * @param  int   $ordercode
     * @param  array $guzzleOptions Additional parameters for the Guzzle client
     * @return array
     */
    public function getByOrder($ordercode, array $guzzleOptions = []): array
    {
        $parameters = ['ordercode' => $ordercode];

        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/transactions'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->Transactions;
    }

    /**
     * List of all transactions that occurred on a given date.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get
     *
     * @param  \DateTimeInterface|string $date
     * @param  array                     $guzzleOptions Additional parameters for the Guzzle client
     * @return array
     */
    public function getByDate($date, array $guzzleOptions = []): array
    {
        $parameters = ['date' => $this->formatDate($date)];

        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/transactions'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->Transactions;
    }

    /**
     * List of all transactions that occurred on a specific clearance date.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get
     *
     * @param  \DateTimeInterface|string $clearancedate
     * @param  array                     $guzzleOptions Additional parameters for the Guzzle client
     * @return array
     */
    public function getByClearanceDate($clearancedate, array $guzzleOptions = []): array
    {
        $parameters = ['clearancedate' => $this->formatDate($clearancedate)];

        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/transactions'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->Transactions;
    }

    /**
     * List of all transactions for a given Source Code for a specific date.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/get
     *
     * @param  string                    $sourcecode
     * @param  \DateTimeInterface|string $date
     * @param  array                     $guzzleOptions Additional parameters for the Guzzle client
     * @return array
     */
    public function getBySourceCode($sourcecode, $date, array $guzzleOptions = []): array
    {
        $parameters = [
            'sourcecode' => $sourcecode,
            'date' => $this->formatDate($date),
        ];

        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/transactions'),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        return $response->Transactions;
    }

    /**
     * Cancel or refund a payment.
     *
     * @see https://developer.vivawallet.com/api-reference-guide/payment-api/#tag/Transactions/paths/~1api~1transactions~1{Id}/delete
     *
     * @param  string       $id            The transaction's unique ID
     * @param  int          $amount        The amount that will be refunded in the currency's smallest denomination (e.g amount in pounds x 100)
     * @param  string|null  $sourceCode    The source from which the funds will be withdrawn. Each source is linked to a wallet. If no sourceCode is set then the funds will be withdrawn from the primary wallet.
     * @param  array        $guzzleOptions Additional parameters for the Guzzle client
     * @return \stdClass
     */
    public function cancel(
        string $id,
        int $amount,
        $sourceCode = null,
        array $guzzleOptions = []
    ) {
        $parameters = array_merge(
            ['amount' => $amount],
            $sourceCode ? ['sourceCode' => $sourceCode] : []
        );

        return $this->client->delete(
            $this->client->getUrl()->withPath("/api/transactions/{$id}"),
            array_merge_recursive(
                [RequestOptions::QUERY => $parameters],
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );
    }

    /**
     * Format a date object to string.
     *
     * @param  \DateTimeInterface|string $date
     * @return string
     */
    protected function formatDate($date): string
    {
        if ($date instanceof \DateTimeInterface) {
            return $date->format('Y-m-d');
        }

        return $date;
    }
}
