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

    /**
     * @test
     */
    public function it_returns_json_string_response()
    {
        $this->delivery->addDelivery(1);
        $this->assertIsString($this->delivery->send());
    }

    /**
     * @test
     */
    public function it_returns_json_with_status_response()
    {
        $this->delivery->addDelivery(1);

        $response = $this->delivery->send();

        $data = json_decode($response, true);

        $this->assertArrayHasKey('status', $data);
    }

    /**
     * @test
     */
    public function it_returns_unauthorized_for_incorrect_login()
    {
        $this->delivery->addDelivery(1);

        $response = $this->delivery->send();

        $data = json_decode($response, true);

        $this->assertArrayHasKey('code',$data);

        $this->assertEquals('1000',$data['code']);
    }
}