SMS
===

> Provider neutral SMS library for PHP 5.5+

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
composer require litgroup/sms=0.5.*
```


Example of usage
----------------

### Message sending

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


### Message logging

All sent messages can be logged. You can implement own custom logger which can store all sent messages in a database.
Logger must implement `LitGroup\Sms\Logger\MessageLoggerInterface` and must be injected into the message service
by calling `MessageServiceInterface::setMessageLogger()`.

Package provides default implementation `LitGroup\Sms\Logger\MessageLogger` which can be used for testing purposes.

**Example:**

```php
use LitGroup\Sms\Logging\MessageLogger;
// ...

$logger = new MessageLogger();

/** @var MessageService $messageService */
$messageService->setMessageLogger($logger);

// Sending of messages...

// Get all sent messages:
$logger->getMessages();

// Get number of sent messages:
$logger->count();
```