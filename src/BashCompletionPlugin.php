<?php

namespace Stecman\Component\Symfony\Console\BashCompletion;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Console\Input\ArgvInput;

class BashCompletionPlugin implements PluginInterface
{
    /**
     * @var \Symfony\Component\Console\Input\ArgvInput
     */
    protected $input;

    public function activate(Composer $composer, IOInterface $io)
    {
        global $application;
        global $__bashCompletionInjected;

        if ($this->getInput()->getFirstArgument() === '_completion' && !$__bashCompletionInjected) {
            $__bashCompletionInjected = true;

            $application->add(new ComposerCompletionCommand());
            $application->run();
            die();
        }
    }

    /**
     * @return ArgvInput
     */
    protected function getInput()
    {
        if (!$this->input) {
            $this->input = new ArgvInput();
        }

        return $this->input;
    }
}