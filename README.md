SMS
===

> Short messaging library for PHP.

[![Version](https://img.shields.io/packagist/v/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![Dev Version](https://img.shields.io/packagist/vpre/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/LitGroup/sms.php/blob/master/LICENSE)
[![Downloads](https://img.shields.io/packagist/dt/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![Build Status](https://travis-ci.org/LitGroup/sms.php.svg?branch=master)](https://travis-ci.org/LitGroup/sms.php)


Available gateway services
--------------------------

* `SmscGateway` — https://smsc.ru

Installation
------------

```
composer require litgroup/sms=~0.2
```

Example of usage
----------------

```php
use LitGroup\Sms\Message;
use LitGroup\Sms\MessageService;
use LitGroup\Sms\Gateway\Smsc\SmscGateway;
use GuzzleHttp\Client as HttpClient;

// SMSC.ru gateway used for example. But any other gateway, can be used.
// GatewayInterface must be implemented.
$gateway = new SmscGateway('mylogin', 'mypassword', new HttpClient());

// Create MessageService.
$messageService = new MessageService($gateway);

// Create some message.
$message = new Message();
$message
    ->setBody('Hello!!!')
    ->addRecipient('+71232223344')
    ->setSender('MyCompany');
    
// Send a message.
$messageService->sendMessage($message);

```