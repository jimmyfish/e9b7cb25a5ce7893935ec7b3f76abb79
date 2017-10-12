#!/bin/bash

echo 'Autofixing coding violations ...'

vendor/friendsofphp/php-cs-fixer/php-cs-fixer -vvv --no-interaction --allow-risky=no --rules=@PSR1 fix src/

vendor/squizlabs/php_codesniffer/scripts/phpcbf src/ -n -p --standard=PSR2 --ignore=*Test.php --ignore=*/Entity/* --ignore=*/js/libs/*