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

interface MailManagerInterface
{
    /**
     * Get a mailer instance by name.
     */
    public function get(string $name): MailerInterface;
}
