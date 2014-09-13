<?php

namespace Stecman\Component\Symfony\Console\BashCompletion;

use Stecman\Component\Symfony\Console\BashCompletion\CompletionCommand;

class ComposerCompletionCommand extends CompletionCommand
{
    protected function runCompletion()
    {
        $context = $this->handler->getContext();
        $words = $context->getWords();

        // Allow completions for global
        if ($words[1] === 'global') {
            $context->setCommandLine(
                preg_replace('/ global/', '', $context->getCommandLine(), 1)
            );

            // Adjust for the removal of the word 'global'
            $context->setCharIndex($context->getCharIndex() - 7);
        }

        return $this->handler->runCompletion();
    }
}