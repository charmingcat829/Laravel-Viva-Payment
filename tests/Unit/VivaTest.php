<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Client;
use Sebdesign\VivaPayments\Facades\Viva;
use Sebdesign\VivaPayments\Test\TestCase;

/** @covers \Sebdesign\VivaPayments\Facades\Viva */
class VivaTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_proxies_the_client(): void
    {
        $viva = Viva::getFacadeRoot();

        $this->assertInstanceOf(Client::class, $viva);
    }
}
