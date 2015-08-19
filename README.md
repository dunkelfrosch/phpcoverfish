# PHPCoverFish

phpCoverFish is an open source php(cli) code coverage pre-processor, used to validate all of your @covers annotations inside your test files before the big code coverage train will run through all of your tests and may collide with bad coverage annotations scattered along the rails.

*This alpha version of phpCoverFish won't be as functional as the coming beta version. Specific commands like coverage warning features, including corresponding threshold breaks and stop-on-error/stop-on-failure parameters are not fully functional yet. As coverfish is still actively  under development, it is a slight possibility that some  options and parameter are not fully working as expected or may have undergone changes in recent versions.*

[![Software License](https://img.shields.io/badge/license-MIT-brightgreen.svg)](LICENSE)
[![Build Status](https://travis-ci.org/dunkelfrosch/phpcoverfish.svg?branch=master)](https://travis-ci.org/dunkelfrosch/phpcoverfish)
[![Scrutinizer Code Quality](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/badges/quality-score.png?b=master)](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/?branch=master)
[![Code Coverage](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/badges/coverage.png?b=development)](https://scrutinizer-ci.com/g/dunkelfrosch/phpcoverfish/?branch=master)
[![Dependency Status](https://gemnasium.com/dunkelfrosch/phpcoverfish.svg)](https://gemnasium.com/dunkelfrosch/phpcoverfish)

## Installation

If you use Composer to manage the dependencies of your project, edit your projects composer.json file and add a dependency on df/phpcoverfish.
Below you can find find a minimal example of a composer.json file that just defines a *dev-time* dependency on PHPCoverFish:

    ...
    {
        "require-dev": {
            "df/phpcoverfish": "0.9.*"
    }
    ...
 
You can also execute this composer command in your console:

    composer require df/php-coverfish

In order to use the current PHPCoverFish release you will need to take down the minimum-stability key in your composer.json file (set "alpha"). Simply place this line above your "extra" property key:

    ...
    "minimum-stability": "alpha",
    "extra": {
    ...

Afterwards, just call *composer install "df/phpcoverfish=0.9.*"* or use *composer update* and create a symbolic link from *vendor/df/coverfish/bin/coverfish.php* to your web application tool directory (this step is optional).

    composer install "df/phpcoverfish=0.9.*"

To provide coverfish system-wide just type the following command:

    composer global require "df/phpcoverfish=0.9.*"

Don't forget to make sure you've got your composers vendor binary path available in your global shell path:

    PATH=$PATH:~/.composer/vendor/bin/

As soon as coverfish will reach it's stable state we will not only provide an additional phar file but also a corresponding download link / installer for **wget** related installation of PHPCoverFish.


## Usage

To call coverfish from your shell after installation use the following two mode's:
*if you bound coverfish in your symfony application, composer will be create a symbolic link inside your bin/ directory 
so you can call this tool like others (phpunit, ...) from this path directly.*

**PHPUnit-Mode** scan (recommended scan mode), using existing phpunit.xml instead of "raw" parameter for scan-path, exclude-path and autoload-file: 

    php ./bin/coverfish scan tests/phpunit.xml \
            --phpunit-config-suite "PHPCoverFish Suite" \ 
            --output-level 1 \
            --no-ansi

for example (using phpunit.xml without any test suite name will take first test suite configuration for this scan) :    
    
    php ./bin/coverfish scan tests/phpunit.xml --output-level 1 --no-ansi

or (using phpunit test suite name "PHPCoverFish Suite" ):

    php ./bin/coverfish scan tests/phpunit.xml --phpunit-config-suite "PHPCoverFish Suite" --output-level 1 --no-ansi   

the result should be:

![phpunit mode result](https://dl.dropbox.com/s/371ea9arp1yvdb9/ss-sample-normal-mode-result-1.png)

**RAW-Mode** scan (alternative scan mode), using additional parameters for required scan-path, autoload-file (exclude path will be used optional here)

    php <path/to/your/coverfish/vendor/>bin/coverfish.php *scan* \
            --raw-scan-path "<path/to/your/phpunit/tests>" \
            --raw-autoload-file "<path/to/your/autoload.php>" \
            --raw-exclude-path "<path/to/your/excluded/test/files>"
    
for example:

    php ./bin/coverfish scan --raw-scan-path tests/ --raw-autoload-file "vendor/autoload.php" --raw-exclude-path "tests/data" --output-level 1 --no-ansi

the result should looking like:

![raw mode result](https://dl.dropbox.com/s/cpr6tp341asxylu/ss-sample-raw-mode-result-1.png)


## PHPCoverfish arguments and parameter

To call the PHPCoverFish help page use:

    php <path/to/your/coverfish/vendor/>bin/coverfish.php help scan

### Arguments

    scan                     scan/analyze command (currently the only available mode of coverfish)
    phpunit-config           path to your project phpunit.xml config file (e.g. tests/phpunit.xml)
                             this argument override all raw- parameter (raw-scan-path, raw-autoload-path ...)
    
### Parameters (optional)

    raw-scan-path            path to your target php unit class test files or a single test file
    raw-exclude-path         exclude a specific path from your planned scan 
    raw-autoload-file        your application used autoload file (psr-0/psr-4 standard)
                             will be replaced by phpunit.xml file in our upcoming beta version
    -f / --output-format     json, text (default) - rendering of scan result output format (json or text)
    -l / --output-level      detail of scan result output (0=minimal, 1=normal(default), 2=detailed)
    --output-prevent-echo    if you fetch results in json format, you can hide direct output and analyse results as as a json object directly,
                             this parameter is mainly used for testing purposes (deprecated, will be replaced by --quiet/-q in beta version)
    --no-ansi                prevent colorful output of rendering results
    
### Parameters (in development, not yet available in alpha)    
    
    --debug                  generate a more detailed debug output of coverfish process
    --stop-on-error          stop on first application error
    --stop-on-failure        stop on first detected coverFish failure 
    --warning-threshold-stop set a warning threshold value, application will break on reaching this number

### Parameters (deprecated, will be removed or renamed in future beta/stable)

    -- verbose               will be handled by option '--output-level <n>'
    -- no-interaction        not necessary, no virtual interaction planned yet
    -- quiet                 will be handled by option '--output-prevent-echo'
                             maybe i'll just rename the current used argument to 'quiet'

## Missing features, annoying bugs and project thoughts

Feel free to contact us for missing features, discovered bugs or nice ideas
around this project :)

*We are currently working on*: 

- validator for global code coverage using @cover in class head annotation of testFile
- File/IO scan mode, currently php reflection ability is used to identify code coverage errors - in future versions a raw scan mode to scan files outside the autoload context will be provided 
- coverage warnings implementation; identify coverage problems or misconfiguration issues in use of phpunit code coverage
- optimization of coverFish's output module; this module is just "meh!"
- mastering coverfish documentation and build up a useful wiki
- include @use statement check
- improve scanner/analyzer speed

## Screenshots, phpCoverFish at work

Depending on the chosen *--output-level* option coverfish will provide different output of test results

Level 0 (—ouput-level **0**)
![Level 0](https://dl.dropbox.com/s/7b6nptkbyiowrx4/ss-output-level-0.png)

Level 1 (—ouput-level **1**)
![Level 1](https://dl.dropbox.com/s/xk43g0gu1ccqtlw/ss-output-level-1.png)

Level 2 (—ouput-level **2**)
![Level 2](https://dl.dropbox.com/s/voyqmf5g9q42ana/ss-output-level-2.png)


## Contribute

PHPCoverFish is still under development and contributors are always welcome!
Feel free to join our development team. Please refer to [CONTRIBUTING.md](https://github.com/dunkelfrosch/phpcoverfish/blob/master/CONTRIBUTING.md) to find out how to contribute to the PHPCoverFish Project.


## License

Copyright (c) 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
                                                                           
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