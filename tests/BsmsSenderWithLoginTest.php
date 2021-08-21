<?php

namespace matejch\bsmsApiHandler\tests;

use matejch\bsmsApiHandler\BsmsSender;
use PHPUnit\Framework\TestCase;

class BsmsSenderWithLoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_json_with_recipients_response_when_login_correct()
    {
        /** DO NOT PUSH WITH REAL LOGIN DETAIL */
        $sender = new BsmsSender('test','test12345');

        $sender->addSms(1,'12345649','message');

        $response = $sender->send(true);

        $data = json_decode($response, true);

        $this->assertArrayHasKey('recipients', $data);
    }
}