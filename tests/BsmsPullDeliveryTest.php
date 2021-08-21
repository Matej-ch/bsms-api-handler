<?php

namespace matejch\bsmsApiHandler\tests;

use matejch\bsmsApiHandler\BsmsPullDelivery;
use PHPUnit\Framework\TestCase;

class BsmsPullDeliveryTest extends TestCase
{
    protected $delivery;

    protected function setUp(): void
    {
        $this->delivery = new BsmsPullDelivery('username','password');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_no_delivery_was_added()
    {
        $this->expectException(\RuntimeException::class);

        $this->delivery->send();
    }
}