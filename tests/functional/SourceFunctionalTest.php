<?php

use Sebdesign\VivaPayments\Source;

class SourceFunctionalTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_adds_a_payment_source()
    {
        $source = app(Source::class);

        $source->create('Site 1', 'site1', 'https://www.domain.com', 'order/failure', 'order/success');
    }
}
