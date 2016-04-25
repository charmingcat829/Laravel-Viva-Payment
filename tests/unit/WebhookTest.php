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
        $verification = ['foo' => 'bar'];

        $this->mockJsonResponses([$verification]);
        $this->mockRequests();

        $webhook = new Webhook($this->client);

        $code = $webhook->getAuthorizationCode();
        $request = $this->getLastRequest();

        $this->assertEquals('GET', $request->getMethod(), 'The request method should be GET.');
        $this->assertEquals($verification, (array) $code);
    }
}
