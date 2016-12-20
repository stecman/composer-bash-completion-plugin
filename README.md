# BASH/ZSH auto-complete plugin for Composer

This is an experimental hack to add [Symfony BASH auto complete](https://github.com/stecman/symfony-console-completion) to Composer via a plugin. It's a pretty slimy hack, but it works without editing Composer's code.

![Composer BASH completion](https://i.imgur.com/MoDWkby.gif)

## Installation

1. Run `composer global require stecman/composer-bash-completion-plugin dev-master`
2. Add a completion hook to your shell's user config file:
  - If you're using BASH, put the following in your `~/.bash_profile` file:

    ```bash
    # Add shell auto-completion for composer
    source "${COMPOSER_HOME-$HOME/.composer}/vendor/stecman/composer-bash-completion-plugin/hooks/bash-completion"
    ```
  - If you're using ZSH, put the following in your `~/.zshrc` file:
    
    ```bash
    # Add shell auto-completion for composer
    source "${COMPOSER_HOME-$HOME/.composer}/vendor/stecman/composer-bash-completion-plugin/hooks/zsh-completion"
    ```
3. Reload the modified shell config (or open a new shell), and enjoy tab completion on Composer

## Explanation

This hacky plugin injects an additional command into the Composer application at runtime. When the plugin in this package is activated and the command line starts with `composer depends _completion`, the plugin effectively reboots the application with the completion command added, and drops `depends` from the command line so that `_completion` becomes the command argument. This used to work without piggy-backing on a command, but an update to composer stopped the original method working (#8).
