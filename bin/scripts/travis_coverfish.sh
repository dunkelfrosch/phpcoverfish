#!/bin/sh
#
# travis call for phpunit code coverage generation
#

php ../../bin/coverfish scan --raw-scan-path ../../tests/ --raw-autoload-file ../../vendor/autoload.php --raw-exclude-path tests/data --output-level 1 --no-ansi

if [ $? -eq 0 ]
then
  echo "\nCoverFish run succeeded"
else
  echo "\nCoverFish run failed" >&2
fi
