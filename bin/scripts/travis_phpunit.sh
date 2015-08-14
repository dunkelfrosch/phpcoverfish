#!/bin/sh
#
# travis call for phpunit check
#

cd ../.. && phpunit -c tests/phpunit.xml --testdox && cd ./bin/scripts

if [ $? -eq 0 ]
then
  echo "\nPHPUnit run succeeded"
else
  echo "\nPHPUnit run failed" >&2
fi