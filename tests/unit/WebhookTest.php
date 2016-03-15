<?php

use Sebdesign\VivaPayments\Webhook;

class WebhookTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_gets_an_authorization_code()
    {
        $webhook = app(Webhook::class);

        $verification = ['foo' => 'bar'];

        $history = $this->mockRequests();
        $this->mockJsonResponses([$verification]);

        $code = $webhook->getAuthorizationCode();
        $request = $history->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($verification, (array) $code);
    }
}
