<?php

namespace Sebdesign\VivaPayments\Test\Functional;

use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\OAuth;
use Sebdesign\VivaPayments\Test\TestCase;

class OAuthTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_requests_an_access_token_and_sets_it_to_the_client()
    {
        $token = app(OAuth::class)->requestToken();

        $this->assertTrue(is_object($token));
        $this->assertObjectHasAttribute('access_token', $token);
        $this->assertObjectHasAttribute('expires_in', $token);
        $this->assertObjectHasAttribute('token_type', $token);

        $bearer = app(Client::class)->authenticateWithBearerToken();

        $this->assertEquals([
            'headers' => [
                'Authorization' => "Bearer {$token->access_token}",
            ],
        ], $bearer);
    }

    /**
     * @test
     * @group functional
     */
    public function it_requests_an_access_token()
    {
        $token = app(OAuth::class)->token(env('VIVA_CLIENT_ID'), env('VIVA_CLIENT_SECRET'));

        $this->assertTrue(is_object($token));
        $this->assertObjectHasAttribute('access_token', $token);
        $this->assertObjectHasAttribute('expires_in', $token);
        $this->assertObjectHasAttribute('token_type', $token);
    }
}
