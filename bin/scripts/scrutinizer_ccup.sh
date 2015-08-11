#!/bin/sh
#
# scrutinizier code coverage upload
#

echo "== sys::_scrutinizer, setup ocular.phar archive";
wget -q -N https://scrutinizer-ci.com/ocular.phar ocular.phar
echo "== sys::_scrutinizer, upload code coverage result ...";
php ocular.phar code-coverage:upload --format=php-clover "../../coverage.clover.xml"
echo "== sys::_scrutinizer, all jobs done";

exit 0;