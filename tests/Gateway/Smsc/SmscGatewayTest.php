<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace Tests\LitGroup\Sms\Gateway\Smsc;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\Exception\SeekException;
use GuzzleHttp\Exception\TransferException;
use GuzzleHttp\Handler\MockHandler;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Middleware;
use GuzzleHttp\Psr7\Response;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\RequestOptions;
use LitGroup\Sms\Gateway\Smsc\SmscGateway;
use LitGroup\Sms\Message;

class SmscGatewayTest extends \PHPUnit_Framework_TestCase
{
    const SMSC_LOGIN = 'mylogin';
    const SMSC_PASS = 'mypassword';
    const SMSC_PASS_MD5 = '34819d7beeabb9260a5c854bc85b3e44';

    const SMSC_API_ENTRY_POINT = 'https://smsc.ru/sys/send.php';

    const MESSAGE_BODY = 'How are you?';
    const MESSAGE_RECIPIENT_1 = '+71111234567890';
    const MESSAGE_RECIPIENT_2 = '+72221234567890';
    const MESSAGE_SENDER = 'LitGroup';

    const HTTP_CONN_TIMEOUT = 10.0;
    const HTTP_TIMEOUT = 20.0;


    /**
     * @var SmscGateway
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

        $this->gateway = new SmscGateway(
            self::SMSC_LOGIN,
            self::SMSC_PASS,
            $this->httpClient,
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
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"id": 123, "cnt": 1}'
            )
        );

        // Sending message:
        $this->gateway->sendMessage($message);

        $this->assertHttpRequestsCount(1);

        $this->assertEquals(self::HTTP_CONN_TIMEOUT, $this->httpContainer[0]['options'][RequestOptions::CONNECT_TIMEOUT]);
        $this->assertEquals(self::HTTP_TIMEOUT, $this->httpContainer[0]['options'][RequestOptions::TIMEOUT]);

        $request = $this->getHttpRequest(0);
        $this->assertSame('POST', $request->getMethod());
        $this->assertSame(self::SMSC_API_ENTRY_POINT, (string) $request->getUri());
        $this->assertEquals(
            $this->getRequestBody($message),
            (string) $request->getBody()
        );
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayErrorResponseException
     */
    public function testSendMessage_ErrorResponse()
    {
        $this->httpMockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                '{"error": "some error message", "error_code": 999, "id": 123}'
            )
        );

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayUnavailableException
     */
    public function testSendMessage_UnexpectedResponseStatus()
    {
        $this->httpMockHandler->append(
            new Response(204)
        );

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayUnavailableException
     */
    public function testSendMessage_InvalidResponseFormat()
    {
        $this->httpMockHandler->append(
            new Response(
                200,
                ['Content-Type' => 'application/json'],
                'invalid json'
            )
        );

        $this->gateway->sendMessage($this->getMessage());
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
     * @expectedException \LitGroup\Sms\Exception\GatewayTransferException
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

    /**
     * @param Message $message
     *
     * @return string
     */
    private function getRequestBody(Message $message)
    {
        $params = [
            'login'   => self::SMSC_LOGIN,
            'psw'     => self::SMSC_PASS_MD5,
            'phones'  => implode(',', $message->getRecipients()),
            'charset' => 'utf-8',
            'mes'     => $message->getBody(),
            'fmt'     => SmscGateway::SMSC_API_RESPONSE_FORMAT,
        ];

        if ($message->getSender() !== null) {
            $params['sender'] = $message->getSender();
        }

        $pairs = [];
        foreach ($params as $k => $v) {
            array_push(
                $pairs,
                sprintf('%s=%s', $k, urlencode($v))
            );
        }

        return implode('&', $pairs);
    }
}
