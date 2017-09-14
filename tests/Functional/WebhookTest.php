<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\Webhook;
use Sebdesign\VivaPayments\Test\TestCase;

class WebhookTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_gets_an_authorization_code()
    {
        $code = app(Webhook::class)->getAuthorizationCode();

        $this->assertObjectHasAttribute('Key', $code);
        $this->assertInternalType('string', $code->Key);
    }
}
