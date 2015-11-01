#!/bin/sh
#
# travis call for phpunit code coverage generation
#

php ../../bin/coverfish scan ../../tests/phpunit.xml --phpunit-config-suite "PHPCoverFish Suite" --output-level 2 --no-ansi

if [ $? -eq 0 ]
then
  echo "\nCoverFish run succeeded"
else
  echo "\nCoverFish run failed" >&2
fi
