<?php

namespace Sebdesign\VivaPayments\Test\Unit;

use Sebdesign\VivaPayments\Source;
use Sebdesign\VivaPayments\Test\TestCase;

class SourceTest extends TestCase
{
    /**
     * @test
     * @group unit
     */
    public function it_adds_a_payment_source()
    {
        $this->mockJsonResponses([[]]);
        $this->mockRequests();

        $source = new Source($this->client);

        $source->create('Site 1', 'site1', 'https://www.domain.com', 'order/failure', 'order/success');
        $request = $this->getLastRequest();

        $this->assertMethod('POST', $request);
        $this->assertBody('Name', 'Site 1', $request);
        $this->assertBody('SourceCode', 'site1', $request);
        $this->assertBody('Domain', 'www.domain.com', $request);
        $this->assertBody('isSecure', '1', $request);
        $this->assertBody('PathFail', 'order/failure', $request);
        $this->assertBody('PathSuccess', 'order/success', $request);
    }
}
