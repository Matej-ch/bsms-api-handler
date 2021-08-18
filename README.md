Bsms api handler
================
Api for sending sms with viamobile bsms

[https://www.smsmarketing.sk/](https://www.smsmarketing.sk/)

[Api info](https://bsms.viamobile.sk/help/v2/en/)

Installation
------------

The preferred way to install this extension is through [composer](http://getcomposer.org/download/).

Either run

```
php composer.phar require --prefer-dist matejch/bsms-api-handler "*"
```

or add

```
"matejch/bsms-api-handler": "*"
```

to the require section of your `composer.json` file.


Usage
-----

First register at https://www.smsmarketing.sk/

For using api username and password is required

### Sending sms

```php

$sender = new \matejch\bsmsApiHandler\BsmsSender($username, $password);
$sender->addSms($id,$phoneNum,$message);
$sender->send();

```



