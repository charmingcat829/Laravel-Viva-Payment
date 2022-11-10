<?php

namespace Sebdesign\VivaPayments\Services;

use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Responses;

class Webhook
{
    public function __construct(protected Client $client)
    {
    }

    /**
     * Get a webhook authorization code.
     *
     * @see https://developer.vivawallet.com/webhooks-for-payments/#generate-a-webhook-verification-key
     *
     * @param  array<string,mixed>  $guzzleOptions  Additional parameters for the Guzzle client
     */
    public function getVerificationKey(array $guzzleOptions = []): Responses\WebhookVerificationKey
    {
        $response = $this->client->get(
            $this->client->getUrl()->withPath('/api/messages/config/token'),
            array_merge_recursive(
                $this->client->authenticateWithBasicAuth(),
                $guzzleOptions
            )
        );

        /** @phpstan-ignore-next-line */
        return new Responses\WebhookVerificationKey(...$response);
    }
}
