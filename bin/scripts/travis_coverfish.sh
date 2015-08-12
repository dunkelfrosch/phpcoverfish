#!/bin/sh
#
# travis call for phpunit code coverage generation
#

echo "== sys::_travis, execute phpCoverFish scan ...";
cd ../../ && php ./bin/coverfish scan tests/ vendor/autoload.php --exclude-path tests/data --output-level 1
cd ./bin/scripts

exit 0;