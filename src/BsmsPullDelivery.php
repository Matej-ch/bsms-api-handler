<?php

namespace matejch\bsmsApiHandler;

use RuntimeException;

/**
 * @property string $username
 * @property string $password
 * @property array $deliveries
 *
 * @property string $deliveryUrl
 */
class BsmsPullDelivery
{
    private $username;
    private $password;
    private $deliveries = [];

    public $deliveryUrl = 'http://api.bsms.viamobile.sk/json/delivery';

    public function __construct($username, $password)
    {
        $this->username = $username;
        $this->password = $password;
    }

    public function addDelivery($id): void
    {
        $this->deliveries[$id] = [
            'id' => $id,
        ];
    }

    /**
     * @return mixed|string
     */
    public function send()
    {

        $data = [];
        $data['username'] = $this->username;
        $data['password'] = $this->password;

        $data['deliveries'] = $this->deliveries;
        $json = json_encode($data);


        // HTTP POST
        $options = [
            'http' => [
                'header'  => "Content-type: application/x-www-form-urlencoded\r\n",
                'method'  => 'POST',
                'content' => $json,
                'ignore_errors' => true
            ],
        ];
        $context  = stream_context_create($options);
        $result = @file_get_contents($this->deliveryUrl, false, $context);

        if($result === false) {
            throw new RuntimeException("HTTP POST request failed");
        }

        $result = json_decode($result, true);
        if($result === false) {
            throw new RuntimeException("Response is not valid JSON");
        }

        if($result['response']['status'] !== '200') {
            $code =  $result['response']['code'] ?? '__code is missing__';
            $desc = $result['response']['description'] ?? '__description is missing__';
            throw new RuntimeException("Exception occured. Error code=" . $code . ', message=' . $desc);
        }

        return $result['response']['delivery'] ?? 'Delivery not found';
    }
}