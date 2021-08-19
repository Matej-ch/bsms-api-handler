<?php

namespace matejch\bsmsApiHandler;

use RuntimeException;

/**
 * @property string $username
 * @property string $password
 * @property string $sender
 * @property string $type
 * @property array $mtSms
 *
 * @property string $sendUrl
 * @property string $validateUrl
 * @property string $statusUrl
 */
class BsmsSender
{
    /** @var string */
    private $username;

    /** @var string */
    private $password;

    /** @var string */
    private $sender;

    /** @var string */
    private $type;

    /** @var string */
    public $sendUrl = 'https://api.bsms.viamobile.sk/json/send';

    /** @var string */
    public $validateUrl = 'https://api.bsms.viamobile.sk/json/validate';

    /** @var string */
    public $statusUrl = 'https://api.bsms.viamobile.sk/json/hlr';

    /** @var array */
    private $mtSms = [];

    /**
     * @param string $username User login
     * @param string $password User password
     * @param string $sender Sender name, max. 11 characters Default: InfoSMS
     * @param string $type gsm/utf8
     */
    public function __construct(string $username, string $password, string $sender = '', string $type = 'gsm')
    {
        $this->username = $username;
        $this->password = $password;
        $this->sender = $sender;
        $this->type = $type;
    }

    /**
     * @param string|int $id Unique message ID
     * @param string|int $msisdn phone number
     * @param string $message body of the sms
     */
    public function addSms($id, $msisdn, string $message): void
    {
        $this->mtSms[$id] = [
            'id' => $id,
            'msisdn' => $this->cleanNum($msisdn),
            'text' => $message,
        ];
    }

    /**
     * Send sms or only validate
     *
     * @param false $validate
     * @return mixed
     */
    public function send(bool $validate = false)
    {

        $url = $validate ? $this->validateUrl : $this->sendUrl;

        $data = [];
        $data['username'] = $this->username;
        $data['password'] = $this->password;

        $data['message'] = [
            'sender' => $this->sender,
            'message' => '',
            'type' => $this->type,
        ];

        $data['recipients'] = $this->mtSms;

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
        $result = @file_get_contents($url, false, $context);

        if($result === false) {
            throw new RuntimeException("HTTP POST request failed");
        }

        $result = json_decode($result, true);
        if($result === false) {
            throw new RuntimeException("Response is not valid JSON");
        }

        if($result['response']['status'] !== '200') {
            $explanations = $this->explanations()[$result['response']['code']] ?? '';

            throw new RuntimeException("Exception occured. Error code= {$result['response']['code']}, message={$result['response']['description']}, $explanations");
        }

        return $result['response']['recipients'];
    }

    /**
     * Clean given phone number for api, cannot start with + sign or 00
     *
     * @param $msisdn
     * @return string
     */
    private function cleanNum($msisdn): string
    {
        $msisdn = (string)$msisdn;

        //remove empty space
        //remove + sign
        $msisdn = str_replace([' ', '+'], '', $msisdn);

        //remove IDD (00) prefix
        if(strpos($msisdn, '00') === 0) {
            $msisdn = substr($msisdn, 2);
        }

        //remove 0 prefix
        if($msisdn[0] === '0') {
            $msisdn = substr($msisdn, 1);
        }

        $firstThree = substr($msisdn, 0, 3);
        if($firstThree !== '420' && $firstThree !== '421') {
            $msisdn = "421$msisdn";
        }

        return (string)$msisdn;
    }

    /**
     * Error messages that can occur during processing request
     *
     * @return string[]
     */
    private function explanations(): array
    {
        return [
            1000 => 'Nesprávne meno alebo heslo',
            1010 => 'Neznámy prefix, použite mobilné prefixy mobilných operátorov',
            1011 => 'Nemáte povolenie na odoslanie SMS do uvedenej siete',
            1012 => 'Sender môže byť len z rozsahu definovaného spoločnosťou Viamobile',
            1013 => 'Správa je dlhšia ako povolený limit',
            1014 => 'ID správy nie je numerické',
            2001 => 'Posielanie SMS nie je povolené. Prosím, kontaktuje Viamobile',
            2002 => 'Nedostatok kreditu',
        ];
    }

    public function getStatus($msisdn)
    {
        $msisdn = $this->cleanNum($msisdn);

        $auth = base64_encode("$this->username:$this->password");
        $options = [
            'http' => [
                'header'  => "Content-Type: text/xml;charset=UTF-8\r\n" .
                    "Authorization: Basic $auth\r\n" .
                    "Host: api.bsms.viamobile.sk\r\n",
                'method'  => 'GET',
                'ignore_errors' => true
            ],
        ];

        $url = "$this->statusUrl?msisdn=$msisdn";
        $context  = stream_context_create($options);
        return @file_get_contents($url, false, $context);
    }
}