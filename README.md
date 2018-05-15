# PHPCoverFish

phpCoverFish (coverfish) is an open source php cli static code validator used for code coverage pre-processing. coverFish 
will analyze all of your @covers annotations inside your test files before the big code coverage train will run through all of
your tests and may collide with bad coverage annotations scattered along the rails. Coverfish is using plugin base validators
and is easy to extend / fulfill changes or extension in code coverage annotations.

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/dunkelfrosch/phpcoverfish.svg?branch=master)](https://travis-ci.org/dunkelfrosch/phpcoverfish)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/?branch=master)
[![Code Climate](https://codeclimate.com/github/dunkelfrosch/phpcoverfish/badges/gpa.svg)](https://codeclimate.com/github/dunkelfrosch/phpcoverfish)
[![Code Coverage](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/badges/coverage.png?b=master)](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/?branch=master)

## Installation

If you use Composer to manage the dependencies of your project, edit your projects composer.json file and add a dependency on df/phpcoverfish.
Below you can find find a minimal example of a composer.json file that just defines a *dev-time* dependency on PHPCoverFish (stable strain):

    ...
    {
        "require-dev": {
            "df/php-coverfish": "~1.0"
    }
    ...
 
You can also execute the following composer command in your console:

    composer require df/php-coverfish

In order to use upcoming PHPCoverFish beta releases you will need to take down the minimum-stability key in your composer.json file (set "beta"). Simply place this line above your "extra" property key:

    ...
    "minimum-stability": "beta",
    "extra": {
    ...

Afterwards, just call *composer install "df/phpcoverfish=~1.0"* or use *composer update* and create a symbolic link from *vendor/df/coverfish/bin/coverfish.php* to your web application tool directory (this step is optional).

    composer install "df/php-coverfish=~1.0"

To provide coverfish system-wide just type the following command:

    composer global require "df/php-coverfish=~1.0"

Don't forget to make sure you've got your composers vendor binary path available in your global shell path:

    PATH=$PATH:~/.composer/vendor/bin/

As soon as coverfish will reach it's stable state we will not only provide an additional phar file but also a corresponding download link / installer for **wget** related installation of PHPCoverFish.


## Usage

To call coverfish from your shell after installation use the following two modes:
*if you bound coverfish in your symfony application, composer will be create a symbolic link inside your bin/ directory 
so you can call this tool like others (phpunit, ...) from this path directly.*

**PHPUnit-Mode** scan (the recommended scan mode), using existing phpunit.xml instead of "raw" parameter for scan-path, exclude-path and autoload-file: 

    php ./bin/coverfish scan tests/phpunit.xml \
            --phpunit-config-suite "PHPCoverFish Suite" \ 
            --output-level 1 \
            --no-ansi

for example (using phpunit.xml without any test suite name will take first test suite configuration for this scan) :    
    
    php ./bin/coverfish scan tests/phpunit.xml --output-level 1 --no-ansi

or (using phpunit test suite name "PHPCoverFish Suite" ):

    php ./bin/coverfish scan tests/phpunit.xml --phpunit-config-suite "PHPCoverFish Suite" --output-level 1 --no-ansi   

the screen result for code with and without called test suite should be the same:

![phpunit mode result](https://dl.dropbox.com/s/nywdxycqqfoo8x8/cf_cli_rawmode_1280x325.png)

**RAW-Mode** scan (alternative scan mode), using additional parameters for required scan-path, autoload-file (exclude path will be used optional here)

    php <path/to/your/coverfish/vendor/>bin/coverfish.php *scan* \
            --raw-scan-path "<path/to/your/phpunit/tests>" \
            --raw-autoload-file "<path/to/your/autoload.php>" \
            --raw-exclude-path "<path/to/your/excluded/test/files>"
    
for example:

    php ./bin/coverfish scan --raw-scan-path tests/ --raw-autoload-file "vendor/autoload.php" --raw-exclude-path "tests/data" --output-level 1 --no-ansi

the result should looking like:
![raw mode result](https://dl.dropbox.com/s/dutbzpnhxbgnrkc/cf_cli_phpunitmode_1280x325.png)


## PHPCoverfish arguments and parameter

To call the PHPCoverFish help page use:

    php <path/to/your/coverfish/vendor/>bin/coverfish.php help scan

### Arguments

    scan                     scan/analyze command (currently the only available mode of coverfish)
    phpunit-config           path to your project phpunit.xml config file (e.g. tests/phpunit.xml)
                             this argument override all raw-parameters (raw-scan-path, raw-autoload-path ...)
    
### Parameters (optional)

    raw-scan-path            path to your target php unit class test files or a single test file
    raw-exclude-path         exclude a specific path from your planned scan 
    raw-autoload-file        your application used autoload file (psr-0/psr-4 standard)
                             will be replaced by phpunit.xml file in our upcoming beta version
    -f | --output-format     json, text (default) - rendering of scan result output format (json or text)
    -l | --output-level      detail of scan result output (0=minimal, 1=normal(default), 2=detailed)
    -n | --no-interaction    not necessary, no virtual interaction planned yet
    -v | --verbose           will be handled by option '--output-level <n>'
    -q | --quiet             if you fetch results in json format, you can hide direct output and analyse results as as a json object directly
            
    --no-ansi                prevent colorful output of rendering results (default: false | 0)
    --stop-on-error          stop on first application error | exception (default: false | 0)
    --stop-on-failure        stop on first detected coverFish scan failure (default: false | 0)
    

## Missing features, annoying bugs and project thoughts

Feel free to contact us for missing features, discovered bugs or nice ideas
around this project :)

*We are currently working on*: 

- File/IO scan mode, currently php reflection ability is used to identify code coverage errors - in future versions a raw scan mode to scan files outside the autoload context will be provided 
- coverage warnings implementation, identify coverage problems or misconfiguration issues in use of phpunit code coverage
- optimization of coverFish's output module; this module is just "bad"
- refactoring of color output module, using symfony outputFormatter
- mastering coverfish documentation and build up a useful wiki
- include @use statement check
- improve scanner/analyzer speed


## Some screenshots of result screens

Depending on the chosen *--output-level* option coverfish will provide different output of test results

using minimal output level (—ouput-level **0**), no errors in code coverage found
![Level 0, test validated](https://dl.dropbox.com/s/ss7nyvryekl4zhu/cf_cli_output_level_0_ansi_1280x130.png)
same mode, errors in code coverage identified
![Level 0, test failed](https://dl.dropbox.com/s/4yuafdw5r10xwv2/cf_cli_output_level_0_ansi_testfail_1280x160.png)

using moderate (default) output level (—ouput-level **1**)
![Level 1, test validated](https://dl.dropbox.com/s/gg7su00ef32y3lx/cf_cli_output_level_1_ansi_1280x681.png)
same mode, errors in code coverage identified and shown detailed
![Level 1, test failed](https://dl.dropbox.com/s/1m0ts3u2yaeaeku/cf_cli_output_level_1_ansi_testfail_1280x535.png)

and using maximum output level, showing partial output of large screen result (—ouput-level **2**)
![Level 2, test validated](https://dl.dropbox.com/s/9z5vkwqvotdmvc8/cf_cli_output_level_2_ansi_1280x557.png)
same mode, errors in code coverage identified and shown more detailed
![Level 2, test failed](https://dl.dropbox.com/s/fpfixam41rzy8rb/cf_cli_output_level_2_ansi_testfail_1280x699.png)


## Version and compatibility

Please use our latest stable version **1.0.1** of phpCoverFish for your productive static code analyzing process.
This Documentation was last updated on **2015-11-98** (_internal version 1.0.1_)

phpCoverFish works fine with **php5.n** and **php7.n**

## Contribute

PHPCoverFish is still under development and contributors are always welcome!
Feel free to join our coverFish distributor team. Please refer to [CONTRIBUTING.md](https://github.com/dunkelfrosch/phpcoverfish/blob/master/CONTRIBUTING.md)
to find out how to contribute to the PHPCoverFish Project.


## License

Copyright (c) 2015 - 2018 Patrick Paechnatz <patrick.paechnatz@gmail.com>
                                                                           
Permission is hereby granted,  free of charge,  to any  person obtaining a 
copy of this software and associated documentation files (the "Software"),
to deal in the Software without restriction,  including without limitation
the rights to use,  copy, modify, merge, publish,  distribute, sublicense,
and/or sell copies  of the  Software,  and to permit  persons to whom  the
Software is furnished to do so, subject to the following conditions:       
                                                                           
The above copyright notice and this permission notice shall be included in 
all copies or substantial portions of the Software.
                                                                           
THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING  BUT NOT  LIMITED TO THE WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR  PURPOSE AND  NONINFRINGEMENT.  IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY,  WHETHER IN AN ACTION OF CONTRACT,  TORT OR OTHERWISE,  ARISING
FROM,  OUT OF  OR IN CONNECTION  WITH THE  SOFTWARE  OR THE  USE OR  OTHER DEALINGS IN THE SOFTWARE.