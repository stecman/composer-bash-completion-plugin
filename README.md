# BASH auto complete plugin for Composer

This is an experimental hack to add [Symfony BASH auto complete](https://github.com/stecman/symfony-console-completion) to Composer via a plugin. It's a pretty slimy hack, but it works without editing Composer's code.

![Composer BASH completion](https://i.imgur.com/MoDWkby.gif)

## Installation

1. Run `composer g require stecman/composer-bash-complete-plugin`
2. Put the following in your bash profile:

    ```bash
    # Modified version of what `composer _completion -g -p composer` generates
    # Composer will only load plugins when a valid composer.json is in its working directory,
    # so  for this hack to work, we are always running the completion command in ~/.composer
    function _composercomplete {
        export COMP_LINE COMP_POINT COMP_WORDBREAKS;

        RESULT=`cd $HOME/.composer && composer _completion`;
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

3. Reload your bash profile (or open a new shell), and enjoy tab completion on Composer

## Explanation

This hacky plugin injects an additional command into the Composer application at runtime. It relies on the fact that when handling an uncaught exception, `Composer\Console\Application::renderException` is called, which in turn calls `getComposer` and causes Composer plugins to be loaded. When the plugin in this package is activated and the first command line argument is `_completion`, the plugin effectively reboots the application with the completion command added.