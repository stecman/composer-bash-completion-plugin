# BASH/ZSH auto-complete plugin for Composer

This is an experimental hack to add [Symfony BASH auto complete](https://github.com/stecman/symfony-console-completion) to Composer via a plugin. It's a pretty slimy hack, but it works without editing Composer's code.

![Composer BASH completion](https://i.imgur.com/MoDWkby.gif)

## Installation

1. Install the plugin:

  ```
  composer global require stecman/composer-bash-completion-plugin dev-master
  ```

2. Generate code to register a completion hook for your shell and Composer configuration:

  ```
  source $(composer config home --global)/vendor/stecman/composer-bash-completion-plugin/generate-hook
  ```

3. Add the registration code to your shell profile:

  - If you're using BASH, copy the output to your `~/.bash_profile`
  - If you're using ZSH, copy the output to your `~/.zshrc`

4. Reload your modified shell config (or open a new shell), and enjoy tab completion on Composer

## Explanation

This hacky plugin injects an additional command into the Composer application at runtime. When the plugin in this package is activated and the command line starts with `composer depends _completion`, the plugin effectively reboots the application with the completion command added, and drops `depends` from the command line so that `_completion` becomes the command argument. This used to work without piggy-backing on a command, but an update to composer stopped the original method working (#8).
