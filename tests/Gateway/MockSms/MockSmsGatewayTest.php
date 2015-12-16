<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Gateway\MockSms;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\SeekException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\RequestOptions;
use LitGroup\Sms\Gateway\MockSms\MockSmsGateway;
use LitGroup\Sms\Message;

class MockSmsGatewayTest extends \PHPUnit_Framework_TestCase
{
    const MOCK_HOST = 'example.com';
    const MOCK_PORT = 6666;
    const MOCK_URL = 'http://example.com:6666/messages/';

    const MESSAGE_BODY = 'How are you?';
    const MESSAGE_RECIPIENT_1 = '+71111234567890';
    const MESSAGE_RECIPIENT_2 = '+72221234567890';
    const MESSAGE_SENDER = 'LitGroup';

    const HTTP_CONN_TIMEOUT = 10.0;
    const HTTP_TIMEOUT = 20.0;

    /**
     * @var MockSmsGateway
     */
    private $gateway;

    /**
     * @var Client
     */
    private $httpClient;

    /**
     * @var MockHandler
     */
    private $httpMockHandler;

    /**
     * @var array
     */
    private $httpContainer;


    protected function setUp()
    {
        $this->httpContainer = [];
        $this->httpMockHandler = new MockHandler();

        $handler = HandlerStack::create($this->httpMockHandler);
        $handler->push(Middleware::history($this->httpContainer));

        $this->httpClient = new Client([
            'handler' => $handler
        ]);

        $this->gateway = new MockSmsGateway(
            $this->httpClient,
            self::MOCK_HOST,
            self::MOCK_PORT,
            self::HTTP_CONN_TIMEOUT,
            self::HTTP_TIMEOUT
        );
    }

    protected function tearDown()
    {
        $this->gateway = null;
        $this->httpClient = null;
        $this->httpMockHandler = null;
        $this->httpContainer = null;
    }

    public function getSendMessageTests()
    {
        return [
            [$this->getMessage()],
            [$this->getMessage()->setSender(null)],
        ];
    }

    /**
     * @dataProvider getSendMessageTests
     */
    public function testSendMessage(Message $message)
    {
        $this->httpMockHandler->append(
            new Response(202),
            new Response(202)
        );

        // Sending message:
        $this->gateway->sendMessage($message);

        $this->assertHttpRequestsCount(2);

        $this->assertEquals(self::HTTP_CONN_TIMEOUT, $this->httpContainer[0]['options'][RequestOptions::CONNECT_TIMEOUT]);
        $this->assertEquals(self::HTTP_TIMEOUT, $this->httpContainer[0]['options'][RequestOptions::TIMEOUT]);

        $request = $this->getHttpRequest(0);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame(self::MOCK_URL, (string) $request->getUri());
        $this->assertJsonStringEqualsJsonString(
            json_encode(
                [
                    'recipient' => $message->getRecipients()[0],
                    'body' => $message->getBody(),
                    'sender' => $message->getSender(),
                ],
                JSON_UNESCAPED_UNICODE
            ),
            (string) $request->getBody()
        );

        $request = $this->getHttpRequest(1);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame('application/json', $request->getHeaderLine('Content-Type'));
        $this->assertSame(self::MOCK_URL, (string) $request->getUri());
        $this->assertJsonStringEqualsJsonString(
            json_encode(
                [
                    'recipient' => $message->getRecipients()[1],
                    'body' => $message->getBody(),
                    'sender' => $message->getSender(),
                ],
                JSON_UNESCAPED_UNICODE
            ),
            (string) $request->getBody()
        );
    }

    public function getSendMessage_HttpException_tests()
    {
        return [
            [$this->getMock(RequestException::class, [], [], '', false, false)],
            [$this->getMock(TransferException::class, [], [], '', false, false)],
            [$this->getMock(SeekException::class, [], [], '', false, false)],
        ];
    }

    /**
     * @dataProvider getSendMessage_HttpException_tests
     *
     * @expectedException \LitGroup\Sms\Exception\GatewayUnavailableException
     */
    public function testSendMessage_HttpException($exception)
    {
        $this->httpMockHandler->append($exception);

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return new Message(
            self::MESSAGE_BODY,
            [
                self::MESSAGE_RECIPIENT_1,
                self::MESSAGE_RECIPIENT_2
            ],
            self::MESSAGE_SENDER
        );
    }

    /**
     * @param integer $id
     *
     * @return Request
     */
    private function getHttpRequest($id)
    {
        return $this->httpContainer[$id]['request'];
    }

    /**
     * @param integer $count
     */
    private function assertHttpRequestsCount($count)
    {
        $this->assertCount($count, $this->httpContainer);
    }
}
