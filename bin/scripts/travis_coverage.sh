#!/bin/sh
#
# travis call for phpunit code coverage generation
#

cd ../.. && phpunit --coverage-clover='coverage.clover.xml' -c ./tests/phpunit.xml >/dev/null && cd ./bin/scripts

if [ $? -eq 0 ]
then
  echo "\nPHPUnit coverage run succeeded"
else
  echo "\nPHPUnit coverage run failed" >&2
fi

exit 0;