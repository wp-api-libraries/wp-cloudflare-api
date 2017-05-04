#!/bin/bash
#
if [ ! -f ./vendor/bin/phpunit ]; then
    composer up
fi

#Get the current working directory because installed_paths doesn't support relative paths
DIR="$( cd "$( dirname "${BASH_SOURCE[0]}" )" && pwd )"

#Set the global-ish variable
./vendor/bin/phpcs --config-set installed_paths $DIR/vendor/wp-coding-standards/wpcs

#Run PHPCS
./vendor/bin/phpcs $(find . -name '*.php')

#Run the local copy of PHPUnit
./vendor/bin/phpunit -c phpunit.xml.dist --coverage-html ./tests/logs/coverage/