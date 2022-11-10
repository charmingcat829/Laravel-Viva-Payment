<?php

namespace Sebdesign\VivaPayments\Test\Functional\Services;

use GuzzleHttp\Exception\GuzzleException;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;
use Sebdesign\VivaPayments\VivaException;

/** @covers \Sebdesign\VivaPayments\Services\OAuth */
class OAuthTest extends TestCase
{
    /**
     * @test
     * @group functional
     * @doesNotPerformAssertions
     * @covers \Sebdesign\VivaPayments\Responses\AccessToken
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_requests_an_access_token_with_the_default_credentials(): void
    {
        Viva::oauth()->requestToken();
    }

    /**
     * @test
     * @group functional
     * @doesNotPerformAssertions
     * @covers \Sebdesign\VivaPayments\Responses\AccessToken
     *
     * @throws GuzzleException
     * @throws VivaException
     */
    public function it_requests_an_access_token_with_the_given_credentials(): void
    {
        Viva::oauth()->requestToken(
            clientId: strval(env('VIVA_CLIENT_ID')),
            clientSecret: strval(env('VIVA_CLIENT_SECRET')),
        );
    }
}
