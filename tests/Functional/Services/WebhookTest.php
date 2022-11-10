<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use Sebdesign\VivaPayments\Services\Webhook;
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
        $verification = app(Webhook::class)->getVerificationKey();

        $this->assertObjectHasAttribute('Key', $verification);
        $this->assertNotEmpty($verification->Key, "Failed asserting that '{$verification->Key}' is not empty.");
    }
}
