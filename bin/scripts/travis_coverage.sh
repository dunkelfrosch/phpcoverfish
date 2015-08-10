#!/bin/sh
#
# travis call for phpunit code coverage generation
#

echo "== sys::_travis, execute phpUnit codeCoverage ...";
cd ../../ && phpunit --coverage-clover=../../coverage.clover.xml --configuration ./tests/phpunit.xml
cd ./bin/scripts

exit 0;