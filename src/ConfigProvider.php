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

use Goletter\Mail\Commands\GenMailCommand;
use Goletter\Mail\Contracts\MailManagerInterface;

class ConfigProvider
{
    public function __invoke(): array
    {
        return [
            'dependencies' => [
                MailManagerInterface::class => MailManager::class,
            ],
            'commands' => [
                GenMailCommand::class,
            ],
            'listeners' => [
            ],
            'publish' => [
                [
                    'id' => 'config',
                    'description' => 'The config for goletter/mail.',
                    'source' => __DIR__ . '/../publish/mail.php',
                    'destination' => BASE_PATH . '/config/autoload/mail.php',
                ],
            ],
        ];
    }
}
