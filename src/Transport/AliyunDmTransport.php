<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail\Transport;

use AlibabaCloud\Client\AlibabaCloud;
use Goletter\Mail\Message;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Address;
use GuzzleHttp\Client;

class AliyunDmTransport implements TransportInterface
{
    protected Client $client;

    protected array $options;

    public function __construct(array $options)
    {
        $this->options = $options;

        $base = 'https://dm.' . $this->options['region_id']. '.aliyuncs.com/';
        $this->client = new Client(['base_uri' => $base, 'timeout' => 10]);

        // 初始化 AlibabaCloud SDK
        AlibabaCloud::accessKeyClient(
            $options['access_key_id'],
            $options['access_secret']
        )->regionId($options['region_id'] ?? 'cn-hangzhou')->asDefaultClient();
    }

    /**
     * 发送邮件
     *
     * @param Message $message
     * @return void
     */
    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        $options = $this->options;
        $toAddress = $message->getTo()[0];
        $form = $message->getFrom()[0];
        $subject = $message->getSubject() ?? '';
        $htmlBody = $message->getHtmlBody() ?? (string) $message->getHtmlBody();

        $params = [
            'Action' => 'SingleSendMail',
            'AccountName' => $form->getAddress(),
            'ReplyToAddress' => 'true',
            'AddressType' => "1",
            'ToAddress' => $toAddress->getAddress(),
            'FromAlias' => $this->options['from_alias'] ?? '',
            'Subject' => $subject,
            'HtmlBody' => $htmlBody,
            'Format' => 'JSON',
            'Version' => '2015-11-23',
            'RegionId' => $this->options['region_id'] ?? '',
            'AccessKeyId' => $this->options['access_key_id'] ?? '',
            'SignatureMethod' => 'HMAC-SHA1',
            'Timestamp' => gmdate("Y-m-d\TH:i:s\Z"),
            'SignatureVersion' => '1.0',
            'SignatureNonce' => uniqid('', true),
        ];

        // 签名
        ksort($params);
        $canonicalizedQuery = '';
        foreach ($params as $key => $val) {
            $canonicalizedQuery .= '&' . rawurlencode($key) . '=' . rawurlencode($val);
        }
        $stringToSign = 'GET&%2F&' . rawurlencode(ltrim($canonicalizedQuery, '&'));
        $signature = base64_encode(hash_hmac('sha1', $stringToSign, $this->options['access_secret'] . '&', true));
        $params['Signature'] = $signature;

        try {
            $response = $this->client->get('/', ['query' => $params]);
            $status = $response->getStatusCode();
            $body = (string) $response->getBody();

            if ($status >= 400) {
                throw new TransportException("Aliyun DirectMail 返回错误: HTTP {$status} => {$body}");
            }

            if ($envelope === null) {
                $envelope = new Envelope(
                    new Address($form->getAddress(), $form->getName()),  // 发件人地址，可改
                    $message->getTo() // 收件人，Email 中已经有
                );
            }
            return new SentMessage($message, $envelope);
        } catch (\Throwable $e) {
            throw new TransportException('AliyunTransport 发送异常: ' . $e->getMessage(), 0, $e);
        }
    }

    public function __toString(): string
    {
        return 'aliyun_dm';
    }
}
