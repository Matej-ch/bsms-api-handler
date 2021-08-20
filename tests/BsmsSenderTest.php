<?php

namespace matejch\bsmsApiHandler\tests;

use matejch\bsmsApiHandler\BsmsSender;
use PHPUnit\Framework\TestCase;

class BsmsSenderTest extends TestCase
{
    protected $sender;

    protected function setUp(): void
    {
        $sender = new BsmsSender('username','password');
    }

    /**
     * @test
     */
    public function it_throws_exception_when_no_sms_was_added()
    {

        $this->sender->send();
    }
}