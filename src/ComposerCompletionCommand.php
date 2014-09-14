<?php

namespace Stecman\Component\Symfony\Console\BashCompletion;

use Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand;
use Stecman\Component\Symfony\Console\BashCompletion\Completion;

class ComposerCompletionCommand extends CompletionCommand
{
    /**
     * Whether or not composer is being run using the global command
     * @var boolean
     */
    protected $isGlobal = false;

    protected function runCompletion()
    {
        $context = $this->handler->getContext();
        $words = $context->getWords();

        // Allow completions for composer global/g
        if (isset($words[1]) && ($words[1] === 'global' || $words[1] === 'g')) {
            
            // Adjust for the removal of the word 'global'/'g'
            $replace = function($matches) use ($context) {
                $context->setCharIndex($context->getCharIndex() - strlen($matches[1]));
                return '';
            };

            $context->setCommandLine(
                preg_replace_callback('/( global| g)( |$)/', $replace, $context->getCommandLine(), 1)
            );

            $this->isGlobal = true;
        }

        // Complete for composer.json in current directory
        if ($this->isGlobal) {
            $composerFile = getcwd() . '/composer.json';
        } else {
            $workingDir = getenv('COMPOSER_CWD');
            $composerFile = $workingDir . '/composer.json';
        }

        if (file_exists($composerFile)) {
            $this->addProjectLocalCompletions(
                json_decode(file_get_contents($composerFile), true)
            );
        }

        // Complete for `help` command's `command` argument
        $application = $this->getApplication();
        $this->handler->addHandler(
            new Completion(
                'help',
                'command_name',
                Completion::TYPE_ARGUMENT,
                function() use ($application) {
                    $names = array_keys($application->all());

                    if ($key = array_search('_completion', $names)) {
                        unset($names[$key]);
                    }

                    return $names;
                }
            )
        );

        return $this->handler->runCompletion();
    }

    /**
     * Setup completions that require a composer.json file to work
     * @param array $config - parsed composer.json
     */
    protected function addProjectLocalCompletions($config)
    {
        $packages = $this->getRequiredPackages($config);

        $completeRequiredPackages = function() use ($packages) {
            return $packages;
        };

        // Complete for `remove` and `update` commands `packages` argument
        $this->handler->addHandler(new Completion('remove', 'packages', Completion::TYPE_ARGUMENT, $completeRequiredPackages));
        $this->handler->addHandler(new Completion('update', 'packages', Completion::TYPE_ARGUMENT, $completeRequiredPackages));
    }

    /**
     * Get a list of package names that are required in a composer.json config
     * @param  array $config
     * @return array
     */
    protected function getRequiredPackages($config)
    {
        $packages = array();

        if (isset($config['require'])) {
            $packages = array_merge($packages, array_keys($config['require']));
        }

        if (isset($config['require-dev'])) {
            $packages = array_merge($packages, array_keys($config['require-dev']));
        }

        return $packages;
    }
}