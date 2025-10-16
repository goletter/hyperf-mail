<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail\Contracts;

interface MailerInterface
{
    /**
     * Render the given message as a view.
     */
    public function render(MailableInterface $mailable): string;

    /**
     * Send a new message using a mailable instance.
     */
    public function sendNow(MailableInterface $mailable): void;

    /**
     * Send a new message using a mailable instance.
     */
    public function send(MailableInterface $mailable): void;

    /**
     * Queue a new e-mail message for sending.
     */
    public function queue(MailableInterface $mailable, ?string $queue = null): bool;

    /**
     * Queue a new e-mail message for sending.
     */
    public function later(MailableInterface $mailable, int $delay, ?string $queue = null): bool;
}
