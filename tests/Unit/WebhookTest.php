<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Test\TestCase;
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

        $this->assertMethod('GET', $request);
        $this->assertEquals($verification, (array) $code);
    }
}
