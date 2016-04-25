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
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $source = new Source($this->client);

        $source->create('Site 1', 'site1', 'https://www.domain.com', 'order/failure', 'order/success');
        $request = $this->getLastRequest();

        parse_str($request->getBody(), $body);

        $this->assertEquals('POST', $request->getMethod(), 'The request method should be POST.');
        $this->assertEquals('Site 1', $body['Name'], 'The source name should be Site 1.');
        $this->assertEquals('site1', $body['SourceCode'], 'The source code should be site1.');
        $this->assertEquals('www.domain.com', $body['Domain'], 'The domain should be www.domain.com.');
        $this->assertEquals('1', $body['isSecure'], 'The domain should be secure.');
        $this->assertEquals('order/failure', $body['PathFail'], 'The fail path should be order/failure.');
        $this->assertEquals('order/success', $body['PathSuccess'], 'The fail path should be order/success.');
    }
}
