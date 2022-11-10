<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;

/** @covers \Sebdesign\VivaPayments\Services\Webhook */
class WebhookTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_gets_a_verification_key(): void
    {
        $verification = Viva::webhooks()->getVerificationKey();

        $this->assertNotEmpty($verification->Key, "Failed asserting that '{$verification->Key}' is not empty.");
    }
}
