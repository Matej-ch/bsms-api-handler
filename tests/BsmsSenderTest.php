<?php

namespace matejch\bsmsApiHandler\tests;

use matejch\bsmsApiHandler\BsmsSender;
use PHPUnit\Framework\TestCase;

class BsmsSenderTest extends TestCase
{
    protected $sender;

    protected function setUp(): void
    {
        $this->sender = new BsmsSender('username','password');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_no_sms_was_added()
    {
        $this->expectException(\RuntimeException::class);

        $this->sender->send();
    }

    /**
     * @test
     */
    public function it_returns_json_string_response()
    {
        $this->sender->addSms(1,'12345649','message');

        $this->assertIsString($this->sender->send());
    }

    /**
     * @test
     */
    public function it_returns_json_with_status_response()
    {
        $this->sender->addSms(1,'12345649','message');

        $response = $this->sender->send();

        $data = json_decode($response, true);

        $this->assertArrayHasKey('status', $data);
    }

    /**
     * @test
     */
    public function it_returns_unauthorized_for_incorrect_login()
    {
        $this->sender->addSms(1,'12345649','message');

        $response = $this->sender->send();

        $data = json_decode($response, true);

        $this->assertArrayHasKey('code',$data);

        $this->assertEquals('1000',$data['code']);
    }


}