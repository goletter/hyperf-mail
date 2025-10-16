<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail\Events;

use Symfony\Component\Mime\Email;

class MailMessageSending
{
    /**
     * The Swift message instance.
     */
    public Email $message;

    /**
     * The message data.
     */
    public array $data;

    /**
     * Create a new event instance.
     */
    public function __construct(Email $message, array $data = [])
    {
        $this->data = $data;
        $this->message = $message;
    }
}
