<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/** @covers \Sebdesign\VivaPayments\Services\Webhook */
class WebhookTest extends TestCase
{
    /**
     * @test
     * @group functional
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_gets_a_verification_key(): void
    {
        $verification = Viva::webhooks()->getVerificationKey();

        $this->assertNotEmpty($verification->Key, "Failed asserting that '{$verification->Key}' is not empty.");
    }
}
