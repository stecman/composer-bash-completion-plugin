<?php

namespace Stecman\Component\Symfony\Console\BashCompletion;

use Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand;

class ComposerCompletionCommand extends CompletionCommand
{
    protected function runCompletion()
    {
        $context = $this->handler->getContext();
        $words = $context->getWords();

        // Allow completions for composer global/g
        if ($words[1] === 'global' || $words[1] === 'g') {
            
            // Adjust for the removal of the word 'global'/'g'
            $replace = function($matches) use ($context) {
                $context->setCharIndex($context->getCharIndex() - strlen($matches[1]));
                return '';
            };

            $context->setCommandLine(
                preg_replace_callback('/( global| g)( |$)/', $replace, $context->getCommandLine(), 1)
            );
        }

        return $this->handler->runCompletion();
    }
}