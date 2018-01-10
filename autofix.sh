#!/usr/bin/env bash


php-cs-fixer fix --rules=@Symfony -vvv --no-interaction src/OfficeBundle

~/.config/composer/vendor/bin/phpcbf src/OfficeBundle -n -p --standard=PSR2 --ignore=*Test.php --ignore=*/Entity/* --ignore=*/Resources/* --ignore=*/js/libs/*

~/.config/composer/vendor/bin/phpcs src/OfficeBundle --ignore-annotations --ignore=src/AppBundle/Entity --ignore=src/OfficeBundle/Resources --standard=PSR2