<?php
/**
 * This file is part of the "litgroup/sms" package.
 *
 * (c) LitGroup <http://litgroup.ru/>
 *
 * This source file is subject to the MIT license that is bundled
 * with this source code in the file LICENSE.
 */

namespace LitGroup\Sms\Gateway\Smsc;

use LitGroup\Sms\Exception\GatewayErrorResponseException;
use LitGroup\Sms\Exception\GatewayTransferException;
use LitGroup\Sms\Exception\GatewayUnavailableException;
use LitGroup\Sms\Message;
use LitGroup\Sms\Gateway\GatewayInterface;
use LitGroup\Sms\Exception\GatewayException;
use GuzzleHttp\ClientInterface as GuzzleHttpClient;
use GuzzleHttp\RequestOptions;
use GuzzleHttp\Exception\GuzzleException;

/**
 * SmscGateway
 *
 * @link https://smsc.ru/
 *
 * @author Roman Shamritskiy <roman@litgroup.ru>
 */
class SmscGateway implements GatewayInterface
{
    /*
     * Successful response example:
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {
     *     "id": 9,
     *     "cnt": 1
     * }
     *
     * Erroneous response examples:
     * ~~~~~~~~~~~~~~~~~~~~~~~~~~~~
     *
     * {
     *     "error": "invalid number",
     *     "error_code": 7,
     *     "id": 175
     * }
     *
     * {
     *     "error": "duplicate request, wait a minute",
     *     "error_code": 9
     * }
     */

    const SMSC_API_ENTRY_POINT = 'https://smsc.ru/sys/send.php';
    const SMSC_API_RESPONSE_FORMAT = 3;

    /**
     * @var string
     */
    private $login;

    /**
     * MD5 of the SMSc password.
     *
     * @var string
     */
    private $password;

    /**
     * @var GuzzleHttpClient
     */
    private $httpClient;

    /**
     * @var float
     */
    private $connectTimeout;

    /**
     * @var float
     */
    private $timeout;

    /**
     * SmscGateway constructor.
     *
     * @param string           $login          SMSc login.
     * @param string           $password       SMSc password.
     * @param GuzzleHttpClient $httpClient     HTTP client.
     * @param float            $connectTimeout The number of seconds to wait while trying to connect to a server.
     * @param float            $timeout        The timeout of the request in seconds.
     */
    public function __construct(
        $login,
        $password,
        GuzzleHttpClient $httpClient,
        $connectTimeout = 0.0,
        $timeout = 0.0
    ) {
        $this->login = $login;
        $this->password = strtolower(md5($password));
        $this->httpClient = $httpClient;
        $this->connectTimeout = (float) $connectTimeout;
        $this->timeout = (float) $timeout;
    }

    /**
     * {@inheritDoc}
     */
    public function sendMessage(Message $message)
    {
        try {
            $httpResponse = $this->httpClient->request('POST', self::SMSC_API_ENTRY_POINT, [
                RequestOptions::CONNECT_TIMEOUT => $this->connectTimeout,
                RequestOptions::TIMEOUT         => $this->timeout,
                RequestOptions::FORM_PARAMS     => $this->prepareFormParams($message),
            ]);

            if ($httpResponse->getStatusCode() !== 200) {
                throw new GatewayUnavailableException('Unexpected response status.');
            }

            $response = $this->unserializeResponse((string) $httpResponse->getBody());

            if (array_key_exists('error', $response)) {
                throw new GatewayErrorResponseException($response['error'], (int) $response['error_code']);
            }

        } catch (GuzzleException $e) {
            throw new GatewayTransferException($e->getMessage(), $e);
        }
    }

    private function prepareFormParams(Message $message)
    {
        $params = [
            'login'   => $this->login,
            'psw'     => $this->password,
            'phones'  => $this->preparePhoneNumbers($message->getRecipients()),
            'charset' => 'utf-8',
            'mes'     => $message->getBody(),
            'fmt'     => self::SMSC_API_RESPONSE_FORMAT,
        ];

        if ($message->getSender() !== null) {
            $params['sender'] = $message->getSender();
        }

        return $params;
    }

    /**
     * @param string[] $numbers
     *
     * @return string
     */
    private function preparePhoneNumbers(array $numbers)
    {
        return implode(',', $numbers);
    }

    /**
     * @param string $responseBody
     *
     * @return array
     * @throws GatewayException
     */
    private function unserializeResponse($responseBody)
    {
        $response = json_decode($responseBody, true);
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new GatewayUnavailableException('Invalid response format.');
        }

        return $response;
    }
}