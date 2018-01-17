SMS
===

🚫 This project is no longer maintained.

> Provider neutral SMS library for PHP 5.5+

[![Version](https://img.shields.io/packagist/v/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![Dev Version](https://img.shields.io/packagist/vpre/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![License](https://img.shields.io/badge/license-MIT-blue.svg)](https://github.com/LitGroup/sms.php/blob/master/LICENSE)
[![Downloads](https://img.shields.io/packagist/dt/litgroup/sms.svg)](https://packagist.org/packages/litgroup/sms)
[![Build Status](https://travis-ci.org/LitGroup/sms.php.svg?branch=master)](https://travis-ci.org/LitGroup/sms.php)

Read the documentation for the last release [here][currentdoc].

Installation
------------

```
composer require litgroup/sms=0.6.*
```


Example of usage
----------------

### Message sending

```php
use LitGroup\Sms\Message;
use LitGroup\Sms\MessageService;
use LitGroup\Sms\Exception\SmsException;

// Some implementation of `LitGroup\Sms\Gateway\GatewayInterface`
$gateway = new SomeGateway();

// Create Short Message Service
$messageService = new MessageService($gateway);

// Create and send some message.
try {
    $messageService->sendMessage(
        'Hello, customer!',
        ['+79991234567'],
        'AcmeCompany'
    );
} catch (SmsException $e) {
    // ...
}
```


### Use cascade of gateways

It's possible to use cascade of gateways of several providers to improve
fault-tolerance. Use `LitGroup\Sms\Gateway\CascadeGateway`.

```php
$cascadeGateway = new CascadeGateway([
    new AGateway(),
    new BGateway(),
]);

$messageService = new MessageService($cascadeGateway);
```


### Logging of exceptions

- Constructor of `MessageService` receives `Psr\Log\LoggerInterface`.
- If you use `CascadeGateway` then inject a logger into the instance of
  `CascadeGateway` too. `Warnings` will be logged if some of gateways are inoperative.


[currentdoc]: https://github.com/LitGroup/sms.php/blob/v0.6.0/README.md
