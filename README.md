# PHPCoverFish

phpCoverFish is an open source php cli code coverage preprocessor, used to validate all of your used cover annotation inside your test files before the real code coverage train will run through all of your tests an may collide with one of bad coverage annotation track obstacles.

*The alpha version of phpCoverFish wont be as functional as the coming beta version. Specific commands like coverage warning features, including corresponding threshold breaks and stop-on-error/stop-on-failure parameters are not fully functional yet.*

[![Build Status](https://travis-ci.org/dunkelfrosch/phpcoverfish.svg?branch=master)](https://travis-ci.org/dunkelfrosch/phpcoverfish)

## Installation

if you use Composer to manage the dependencies of your project, edit your projects composer.json file and add a dependency on df/phpcoverfisch. lines down below you can find find a minimal example of a composer.json file that just defines a *dev-time* dependency on PHPCoverFish:

    ...
    {
        "require-dev": {
            "df/phpcoverfish": "0.9.*"
    }
    ...
    
or just execute this composer command into your console

    composer require df/php-coverfish

you've take down minimum-stability key in your composer.json file (set "alpha"), to use the alpha version of current coverfish release
place this line above your "extra" property key:

    ...
    "minimum-stability": "alpha",
    "extra": {
    ...

after that, just call composer install "df/phpcoverfish=0.9.*" or simply composer update and create (optional) a symbolic link from *vendor/df/coverfish/bin/coverfish.php* to your web application tool directory.

    composer install "df/phpcoverfish=0.9.*"

you can also provide coverfish systemwide by typing the following command:

    composer global require "df/phpcoverfish=0.9.*"

make sure, you've composers vendor binary path in your global shell path available.

    PATH=$PATH:~/.composer/vendor/bin/

I'll also provide an additional phar file as soon as we close coverfish will reach it's stable state and provide a corresponding download link / installer for wget related installation of this tool also.


## Usage

after installation you can call coverfish from your shell using the following basic syntax:

    php <path/to/your/coverfish/vendor/>bin/coverfish.php *scan* <path/to/your/phpunit>/tests <path/to/your/autoload.php>

for example

    cd /www/SymfonySampleApp
    php vendor/df/coverfish/bin/coverfish.php scan tests/ app/autoload.php


## PHPCoverfish arguments and parameter

you can call phpCoverFish help page directly

    php <path/to/your/coverfish/vendor/>bin/coverfish.php help scan

### arguments

    scan                     scan/analyze command (currently the only available mode of coverfish)

### parameter (required)

    scan-path                path to your target php unit class test files or just a single test file
    autoload-file            your application used autoload file (psr-0/psr-4 standard)

### parameter (optional)

    -f / --output-format     json, text (default) - rendering of scan result output format (json or text)
    -l / --output-level      detail of scan result output (0=minimal, 1=normal(default), 2=detailed)
    --output-prevent-echo    if you fetch results in json format, you can hide direct output and analyse result as json object directly,
                             this parameter is used for testing purpose mainly
    --no-ansi                prevent colored output of rendering results
    
### parameter (in development, not available in alpha)    
    
    --debug                  generate a more detailed debug output of coverfish process
    --stop-on-error          stop on first application error
    --stop-on-failure        stop on first detected coverFish failure 
    --warning-threshold-stop set a warning threshold value, application will break on reaching this number

### parameter (deprecated, will be removed in future version(s))

    -- verbose               will be handled by option '--output-level <n>'
    -- no-interaction        not necessary, no virtual interaction planed yet
    -- quiet                 will be handled by option '--output-prevent-echo'
                             my be i'll just rename the current used argument to 'quiet'

## Missing features, annoying bugs and project mind thoughts

feel free to contact me for missing features, any discovered bugs or nice ideas
around this project :)

*I'm currently working on*: 

- config file support (as used and well known in phpunit)
- raw scan mode, currently i used php reflection ability to identify code coverage errors. in future version i'll provide a raw scan mode to scan files outside the autoload context
- coverage warnings implementation, identify coverage problems or miss configuration issues in use of phpunit code coverage.
- refactoring of coverFish's output module, this module is just "meh!"
- mastering coverfish documentation


## Screenshots, phpCoveFish at work

depends on choosen --output-level option coverfish will provide different output of test results

Level 0 (—ouput-level **0**)
![Level 0](https://dl.dropbox.com/s/7b6nptkbyiowrx4/ss-output-level-0.png)

Level 1 (—ouput-level **1**)
![Level 1](https://dl.dropbox.com/s/xk43g0gu1ccqtlw/ss-output-level-1.png)

Level 2 (—ouput-level **2**)
![Level 2](https://dl.dropbox.com/s/voyqmf5g9q42ana/ss-output-level-2.png)



## Contribute

PHPCoverFish is still under development and contributors are always welcome, so feel free to join our development team as contributor! Please refer to [CONTRIBUTING.md](https://github.com/dunkelfrosch/phpcoverfish/blob/master/CONTRIBUTING.md) for information on how to contribute to my PHPCoverFish Project.


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
