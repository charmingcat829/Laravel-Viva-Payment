<?php

use Sebdesign\VivaPayments\Source;

class SourceTest extends TestCase
{
    /**
     * @test
     * @group functional
     */
    public function it_adds_a_payment_source()
    {
        $source = app(Source::class);

        $history = $this->mockRequests();
        $this->mockJsonResponses([[]]);

        $source->create('Site 1', 'site1', 'https://www.domain.com', 'order/failure', 'order/success');
        $request = $history->getLastRequest();

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals('Site 1', $request->getBody()->getField('Name'), 'The source name should be Site 1.');
        $this->assertEquals('site1', $request->getBody()->getField('SourceCode'), 'The source code should be site1.');
        $this->assertEquals('www.domain.com', $request->getBody()->getField('Domain'), 'The domain should be www.domain.com.');
        $this->assertTrue($request->getBody()->getField('isSecure'), 'The domain should be secure.');
        $this->assertEquals('order/failure', $request->getBody()->getField('PathFail'), 'The fail path should be order/failure.');
        $this->assertEquals('order/success', $request->getBody()->getField('PathSuccess'), 'The fail path should be order/success.');
    }
}