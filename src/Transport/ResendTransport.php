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

use Goletter\Mail\Message;
use Symfony\Component\Mailer\Envelope;
use Symfony\Component\Mailer\Exception\TransportException;
use Symfony\Component\Mailer\SentMessage;
use Symfony\Component\Mailer\Transport\TransportInterface;
use Symfony\Component\Mime\RawMessage;
use Symfony\Component\Mime\Address;
use Resend;

class ResendTransport implements TransportInterface
{
    protected $resend;

    public function __construct(array $options)
    {
        $this->resend = Resend::client($options['access_key_id']);
    }

    /**
     * 发送邮件
     *
     * @param Message $message
     * @return void
     */
    public function send(RawMessage $message, Envelope $envelope = null): ?SentMessage
    {
        try {
            $toAddress = $message->getTo()[0];
            $form = $message->getFrom()[0];
            $subject = $message->getSubject() ?? '';
            $htmlBody = $message->getHtmlBody() ?? (string) $message->getHtmlBody();

            $this->resend->emails->send([
                'from' => $form->getAddress(),
                'to' => [$toAddress->getAddress()],
                'subject' => $subject,
                'html' => $htmlBody,
                'reply_to' => $form->getAddress()
            ]);

            if ($envelope === null) {
                $envelope = new Envelope(
                    new Address($form->getAddress(), $form->getName()),  // 发件人地址，可改
                    $message->getTo() // 收件人，Email 中已经有
                );
            }
            return new SentMessage($message, $envelope);
        }  catch (\Throwable $e) {
            throw new TransportException('resend 发送异常: ' . $e->getMessage(), 0, $e);
        }
    }

    public function __toString(): string
    {
        return 'resend';
    }
}
