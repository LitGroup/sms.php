<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Gateway\MockSms;

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\GuzzleException;
use GuzzleHttp\RequestOptions;
use LitGroup\Sms\Exception\GatewayUnavailableException;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Message;

/**
 * Gateway for mock SMS server.
 *
 * @link https://github.com/LitGroup/mock-sms-service
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class MockSmsGateway implements GatewayInterface
{
    const DEFAULT_PORT = '9931';

    /**
     * @var ClientInterface
     */
    private $httpClient;

    /**
     * @var string
     */
    private $url;

    /**
     * @var float
     */
    private $connectTimeout;

    /**
     * @var float
     */
    private $timeout;


    /**
     * MockSmsGateway constructor.
     *
     * @param ClientInterface $httpClient GuzzleHttp Client.
     * @param string          $host       MockSms hostname.
     * @param string          $port       MockSms port (default: 9931).
     * @param float           $connectTimeout The number of seconds to wait while trying to connect to a server.
     * @param float           $timeout        The timeout of the request in seconds.
     */
    public function __construct(
        ClientInterface $httpClient,
        $host,
        $port = MockSmsGateway::DEFAULT_PORT,
        $connectTimeout = 0.0,
        $timeout = 0.0
    ) {
        $this->httpClient = $httpClient;
        $this->url = "http://{$host}:{$port}/messages/";
        $this->connectTimeout = $connectTimeout;
        $this->timeout = $timeout;
    }

    /**
     * @inheritDoc
     */
    public function sendMessage(Message $message)
    {
        try {
            foreach ($message->getRecipients() as $recipient) {
                $this->doSendMessage(
                    $recipient,
                    $message->getSender(),
                    $message->getBody()
                );
            }
        } catch (GuzzleException $e) {
            throw new GatewayUnavailableException('MockSms gateway is unavailable.', 0, $e);
        }
    }

    /**
     * @param string $recipient
     * @param string $sender
     * @param string $body
     *
     * @throws GuzzleException
     */
    private function doSendMessage($recipient, $sender, $body)
    {
        $this->httpClient->request('POST', $this->url, [
            RequestOptions::CONNECT_TIMEOUT => $this->connectTimeout,
            RequestOptions::TIMEOUT         => $this->timeout,
            RequestOptions::HEADERS         => ['Content-Type' => 'application/json'],
            RequestOptions::BODY            => $this->createJsonRequest($recipient, $sender, $body),
        ]);
    }

    /**
     * @param string $recipient
     * @param string $sender
     * @param string $body
     *
     * @return string
     */
    private function createJsonRequest($recipient, $sender, $body)
    {
        return json_encode(
            [
                'recipient' => $recipient,
                'sender' => $sender,
                'body' => $body,
            ],
            JSON_UNESCAPED_UNICODE
        );
    }

}