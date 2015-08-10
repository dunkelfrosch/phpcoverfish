#!/bin/sh
#
# scrutinizier code coverage upload
#

echo "== sys::_scrutinizer, setup ocular.phar archive";
wget -q -N https://scrutinizer-ci.com/ocular.phar ocular.phar
echo "== sys::_scrutinizer, upload code coverage result ...";
php ocular.phar code-coverage:upload --access-token="fd6bbcb81e165753ba1e9851e96294b54e488ddb5b410d7f031711467761855a" --format=php-clover ../../coverage.clover.xml >/dev/null
echo "== sys::_scrutinizer, all jobs done";

exit 0;