<?php

declare(strict_types=1);
/**
 * This file is part of goletter/mail.
 *
 * @link     https://github.com/goletter/hyperf-mail
 * @contact  goletter@outlook.com
 * @license  https://github.com/goletter/hyperf-mail/blob/master/LICENSE
 */
namespace Goletter\Mail\Commands;

use Hyperf\Devtool\Generator\GeneratorCommand;

class GenMailCommand extends GeneratorCommand
{
    public function __construct()
    {
        parent::__construct('gen:mail');
        $this->setDescription('Create a new email class');
    }

    /**
     * Get the stub file for the generator.
     */
    protected function getStub(): string
    {
        return __DIR__ . '/stubs/mail.stub';
    }

    /**
     * Get the default namespace for the class.
     */
    protected function getDefaultNamespace(): string
    {
        return $this->getConfig()['namespace'] ?? 'App\\Mail';
    }
}
