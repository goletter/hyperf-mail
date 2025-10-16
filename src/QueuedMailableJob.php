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

use Hyperf\AsyncQueue\Job;
use Hyperf\Context\ApplicationContext;
use Goletter\Mail\Contracts\MailableInterface;
use Goletter\Mail\Contracts\MailManagerInterface;

class QueuedMailableJob extends Job
{
    public function __construct(public MailableInterface $mailable)
    {
    }

    public function handle(): void
    {
        try {
            $this->mailable->send(ApplicationContext::getContainer()->get(MailManagerInterface::class));
        } catch (\Throwable $e) {
            var_dump('=========== Email Throwable $e=============', $e);
            var_dump('=========== Email Throwable =============', $e->getFile(), $e->getLine(), $e->getMessage());
        }

    }
}
