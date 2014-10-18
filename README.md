# BASH/ZSH auto-complete plugin for Composer

This is an experimental hack to add [Symfony BASH auto complete](https://github.com/stecman/symfony-console-completion) to Composer via a plugin. It's a pretty slimy hack, but it works without editing Composer's code.

![Composer BASH completion](https://i.imgur.com/MoDWkby.gif)

## Installation

1. Run `composer global require stecman/composer-bash-completion-plugin dev-master`
2. Add a completion hook to your shell's user config file:
  - If you're using BASH, put the following in your `~/.bash_profile` file:

    ```bash
    # Modified version of what `composer _completion -g -p composer` generates
    # Composer will only load plugins when a valid composer.json is in its working directory,
    # so  for this hack to work, we are always running the completion command in ~/.composer
    function _composercomplete {
        export COMP_LINE COMP_POINT COMP_WORDBREAKS;
        local -x COMPOSER_CWD=`pwd`
        local RESULT STATUS

        # Honour the COMPOSER_HOME variable if set
        local composer_dir=$COMPOSER_HOME
        if [ -z "$composer_dir" ]; then
            composer_dir=$HOME/.composer
        fi

        RESULT=`cd $composer_dir && composer _completion`;
        STATUS=$?;

        if [ $STATUS -ne 0 ]; then
            echo $RESULT;
            return $?;
        fi;

        local cur;
        _get_comp_words_by_ref -n : cur;

        COMPREPLY=(`compgen -W "$RESULT" -- $cur`);

        __ltrim_colon_completions "$cur";
    };
    complete -F _composercomplete composer;
    ```
  - If you're using ZSH, put the following in your `~/.zshrc` file:
    
    ```bash
    function _composer {
        # Emulate BASH's command line contents variable
        local -x COMP_LINE="$words"

        # Emulate BASH's cursor position variable, setting it to the end of the current word.
        local -x COMP_POINT
        (( COMP_POINT = ${#${(j. .)words[1,CURRENT]}} ))

        # Honour the COMPOSER_HOME variable if set
        local composer_dir=$COMPOSER_HOME
        if [ -z "$composer_dir" ]; then
            composer_dir=$HOME/.composer
        fi
    
        local RESULT STATUS
        local -x COMPOSER_CWD=`pwd`
        RESULT=("${(@f)$( cd $composer_dir && composer _completion )}")
        STATUS=$?;
    
        # Bail out if PHP didn't exit cleanly
        if [ $STATUS -ne 0 ]; then
            echo $RESULT;
            return $?;
        fi;
    
        compadd -- $RESULT
    };
    
    compdef _composer composer;
    ```
3. Reload the modified shell config (or open a new shell), and enjoy tab completion on Composer

## Explanation

This hacky plugin injects an additional command into the Composer application at runtime. It relies on the fact that when handling an uncaught exception, `Composer\Console\Application::renderException` is called, which in turn calls `getComposer` and causes Composer plugins to be loaded. When the plugin in this package is activated and the first command line argument is `_completion`, the plugin effectively reboots the application with the completion command added.
