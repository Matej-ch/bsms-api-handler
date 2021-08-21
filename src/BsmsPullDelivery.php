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
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var array */
    private $deliveries = [];

    /** @var string */
    public $deliveryUrl = 'https://api.bsms.viamobile.sk/json/delivery';

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

        if(empty($this->deliveries)) {
            throw new RuntimeException("No deliveries added");
        }

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

        return json_encode($result['response']);
    }
}