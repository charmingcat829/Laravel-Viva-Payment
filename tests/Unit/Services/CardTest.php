<?php

namespace Sebdesign\VivaPayments\Test\Unit\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/**
 * @covers \Sebdesign\VivaPayments\Client
 * @covers \Sebdesign\VivaPayments\Services\Card
 */
class CardTest extends TestCase
{
    /**
     * @test
     * @group unit
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_creates_a_card_token(): void
    {
        $this->mockJsonResponses(['token' => 'ct_480c964156d949c19abe1b1061b21108']);
        $this->mockRequests();

        $this->client->withToken('test');

        $token = $this->client->cards()->createToken('6cffe5bf-909c-4d69-b6dc-2bef1a6202f7');

        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertJsonBody('transactionId', '6cffe5bf-909c-4d69-b6dc-2bef1a6202f7', $request);
        $this->assertEquals('ct_480c964156d949c19abe1b1061b21108', $token, 'The card token should be ct_480c964156d949c19abe1b1061b21108');
    }
}
