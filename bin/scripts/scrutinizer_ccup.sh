#!/bin/sh
#
# scrutinizier code coverage upload
#

wget -q -N https://scrutinizer-ci.com/ocular.phar ocular.phar
php ocular.phar code-coverage:upload --format=php-clover "../../coverage.clover.xml"
