<?php

use Sebdesign\VivaPayments\Webhook;

class WebhookFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_gets_an_authorization_code()
    {
        $webhook = app(Webhook::class);

        $code = $webhook->getAuthorizationCode();

        $this->assertObjectHasAttribute('Key', $code);
        $this->assertTrue(is_string($code->Key));
    }
}
