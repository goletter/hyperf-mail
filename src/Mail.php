<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail;

use Hyperf\Context\ApplicationContext;
use Goletter\Mail\Contracts\MailManagerInterface;

/**
 * @method static \Goletter\Mail\PendingMail to(mixed $users)
 * @method static \Goletter\Mail\PendingMail cc(mixed $users)
 * @method static \Goletter\Mail\PendingMail bcc(mixed $users)
 * @method static bool later(\Goletter\Mail\Contracts\MailableInterface $mailable, int $delay, ?string $queue = null)
 * @method static bool queue(\Goletter\Mail\Contracts\MailableInterface $mailable, ?string $queue = null)
 * @method static null|int send(\Goletter\Mail\Contracts\MailableInterface $mailable)
 *
 * @see \Goletter\Mail\MailManager
 */
abstract class Mail
{
    public static function __callStatic(string $method, array $args)
    {
        $instance = static::getManager();

        return $instance->{$method}(...$args);
    }

    public static function mailer(string $name): PendingMail
    {
        return new PendingMail(static::getManager()->get($name));
    }

    protected static function getManager(): MailManagerInterface
    {
        return ApplicationContext::getContainer()->get(MailManagerInterface::class);
    }
}
