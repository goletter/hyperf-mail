<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail\Concerns;

use Hyperf\Collection\Collection;
use Goletter\Contract\HasMailAddress;
use Goletter\Mail\PendingMail;

trait PendingMailable
{
    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param Collection|HasMailAddress|HasMailAddress[]|string|string[] $users
     */
    public function to(array|Collection|HasMailAddress|string $users): PendingMail
    {
        return (new PendingMail($this))->to($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param Collection|HasMailAddress|HasMailAddress[]|string|string[] $users
     */
    public function cc(array|Collection|HasMailAddress $users): PendingMail
    {
        return (new PendingMail($this))->cc($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     *
     * @param Collection|HasMailAddress|HasMailAddress[]|string|string[] $users
     */
    public function bcc(array|Collection|HasMailAddress $users): PendingMail
    {
        return (new PendingMail($this))->bcc($users);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     */
    public function locale(string $locale): PendingMail
    {
        return (new PendingMail($this))->locale($locale);
    }

    /**
     * Begin the process of mailing a mailable class instance.
     */
    public function mailer(string $name): PendingMail
    {
        return (new PendingMail($this))->mailer($name);
    }
}
