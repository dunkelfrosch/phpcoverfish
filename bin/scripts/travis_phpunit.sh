#!/bin/sh
#
# travis call for phpunit check
#

echo "== sys::_travis, execute phpUnit tests ...";
cd ../../ && phpunit --testdox --configuration ./tests/phpunit.xml
cd ./bin/scripts

exit 0;