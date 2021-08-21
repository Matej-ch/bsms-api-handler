<?php

namespace matejch\bsmsApiHandler\tests;

use matejch\bsmsApiHandler\BsmsPullDelivery;
use PHPUnit\Framework\TestCase;

class BsmsPullDeliveryWithLoginTest extends TestCase
{
    /**
     * @test
     */
    public function it_returns_json_with_recipients_response_when_login_correct()
    {
        /** DO NOT PUSH WITH REAL LOGIN DETAIL */
        $delivery = new BsmsPullDelivery('test','test12345');

        $delivery->addDelivery(1);

        $response = $delivery->send();

        $data = json_decode($response, true);

        $this->assertArrayHasKey('deliveries', $data);
    }
}