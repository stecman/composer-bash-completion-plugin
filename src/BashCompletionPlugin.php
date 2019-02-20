<?php

namespace Stecman\Component\Symfony\Console\BashCompletion;

use Composer\Composer;
use Composer\IO\IOInterface;
use Composer\Plugin\PluginInterface;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Input\ArgvInput;

class BashCompletionPlugin implements PluginInterface
{
    /**
     * @var \Symfony\Component\Console\Input\ArgvInput
     */
    protected $input;

    public function activate(Composer $composer, IOInterface $io)
    {
        /** @var Application $application */
        global $argv;
        global $application;
        global $__bashCompletionInjected;

        if (count($argv) < 2) {
            return;
        }

        // Inject completion command when the command line is `composer depends _completion`
        if ($argv[1] == 'depends' && $argv[2] == '_completion' && !$__bashCompletionInjected) {
            $__bashCompletionInjected = true;

            // Drop the original command name argument so that "_completion" takes its place
            array_splice($argv, 1, 1);
            $input = new ArgvInput($argv);

            $application->add(new ComposerCompletionCommand());
            $application->run($input);
            die();
        }
    }
}