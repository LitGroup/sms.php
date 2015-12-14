SMS
===

> Short messaging library for PHP.

Available gateway services
--------------------------

* `SmscGateway` — https://smsc.ru

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