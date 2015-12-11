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

use GuzzleHttp\ClientInterface;
use GuzzleHttp\Exception\RequestException;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use LitGroup\Sms\Gateway\Smsc\SmscGateway;
use LitGroup\Sms\Message;

class SmscGatewayTest extends \PHPUnit_Framework_TestCase
{
    const API_ENTRY_POINT = 'https://smsc.ru/sys/send.php';
    const LOGIN = 'superman';
    const PASSWORD = 'krypton';
    const PASSWORD_HASH = 'b785359bf4145b24762ff6940144a24f';
    const CONNECT_TIMEOUT = 10.0;
    const TIMEOUT = 20.0;

    const MESSAGE_BODY = 'I came to the world!';
    const RECIPIENT_1 = '+79991112233';
    const RECIPIENT_2 = '+79994445566';
    const SENDER = 'LitGroup';

    const FORM_PHONES = '+79991112233,+79994445566';
    const FORM_CHARSET = 'utf-8';
    const FORM_FORMAT = 3;

    const SUCCESS_JSON = '{"id": 99, "cnt": 2}';

    const ERROR_JSON = '{"error": "error_msg", "error_code": 666}';

    /**
     * @var SmscGateway
     */
    private $gateway;

    /**
     * @var ClientInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private $httpClient;


    protected function setUp()
    {
        $this->httpClient = $this->getMock(ClientInterface::class);
        $this->gateway = new SmscGateway(
            self::LOGIN,
            self::PASSWORD,
            $this->httpClient,
            self::CONNECT_TIMEOUT,
            self::TIMEOUT
        );
    }

    protected function tearDown()
    {
        $this->gateway = null;
        $this->httpClient = null;
    }

    public function getSendMessageTests()
    {
        return [
            [new Message(self::MESSAGE_BODY, [self::RECIPIENT_1, self::RECIPIENT_2])],
            [new Message(self::MESSAGE_BODY, [self::RECIPIENT_1, self::RECIPIENT_2], self::SENDER)],
        ];
    }

    /**
     * @dataProvider getSendMessageTests
     */
    public function testSendMessage(Message $message)
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->with(
                $this->anything(),
                $this->anything(),
                $this->anything()
            )
            ->willReturnCallback(function ($method, $url, $options) use ($message) {
                $this->assertSame('POST', $method);
                $this->assertSame(self::API_ENTRY_POINT, $url);

                $this->assertArrayHasKey(RequestOptions::CONNECT_TIMEOUT, $options);
                $this->assertEquals(self::CONNECT_TIMEOUT, $options[RequestOptions::CONNECT_TIMEOUT], '', 0.1);

                $this->assertArrayHasKey(RequestOptions::TIMEOUT, $options);
                $this->assertEquals(self::TIMEOUT, $options[RequestOptions::TIMEOUT], '', 0.1);

                $this->assertArrayHasKey(RequestOptions::FORM_PARAMS, $options);

                $expectedParameters = [
                    'login'   => self::LOGIN,
                    'psw'     => self::PASSWORD_HASH,
                    'phones'  => self::FORM_PHONES,
                    'charset' => self::FORM_CHARSET,
                    'mes'     => $message->getBody(),
                    'fmt'     => self::FORM_FORMAT,
                ];

                if ($message->getSender()) {
                    $expectedParameters['sender'] = $message->getSender();
                }

                $this->assertEquals($expectedParameters, $options[RequestOptions::FORM_PARAMS]);

                return $this->getMockForHttpResponse(200, self::SUCCESS_JSON);
            });

        $this->gateway->sendMessage($message);
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function testSendMessage_ResponseStatusNot200()
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->getMockForHttpResponse(500, ''));

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     * @expectedExceptionMessage error_msg
     * @expectedExceptionCode 666
     */
    public function testSendMessage_ErrorResponse()
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->getMockForHttpResponse(200, self::ERROR_JSON));

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function testSendMessage_ResponseWithInvalidJson()
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willReturn($this->getMockForHttpResponse(200, 'invalid json'));

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @expectedException \LitGroup\Sms\Exception\GatewayException
     */
    public function testSendMessage_GuzzleException()
    {
        $this->httpClient
            ->expects($this->once())
            ->method('request')
            ->willThrowException($this->getMock(RequestException::class, [], [], '', false, false));

        $this->gateway->sendMessage($this->getMessage());
    }

    /**
     * @return ResponseInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForHttpResponse($statusCode, $body)
    {
        $response = $this->getMock(ResponseInterface::class);

        $response
            ->expects($this->any())
            ->method('getStatusCode')
            ->willReturn($statusCode);
        $response
            ->expects($this->any())
            ->method('getBody')
            ->willReturn($this->getMockForStream($body));

        return $response;
    }

    /**
     * @return StreamInterface|\PHPUnit_Framework_MockObject_MockObject
     */
    private function getMockForStream($body)
    {
        $stream = $this->getMock(StreamInterface::class);

        $stream
            ->expects($this->any())
            ->method('__toString')
            ->willReturn($body);

        return $stream;
    }

    /**
     * @return Message
     */
    private function getMessage()
    {
        return new Message(self::MESSAGE_BODY, [self::RECIPIENT_1, self::RECIPIENT_2], self::SENDER);
    }
}
