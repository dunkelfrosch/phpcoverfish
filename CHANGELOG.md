# ChangeLog of PHPCoverFish 1.0.2

All notable changes of the PHPCoverFish release series are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.

_This PHPCoverFish changeLog documentation start with version 0.9.4 (2015-08-09)_

## [1.0.2], 2018-05-15:
_current_

### Fixed

- composer dependency issue(s)
- open github issues by @ktomk
- minor fixes in script permissions
- minor fixes in code quality

### Changes

- add php 7.0/php7.1 compatibility
- remove php5.5.n / php5.6.n dependencies
- documentation / project status notes

## [1.0.1], 2015-11-09:

### Fixed

- wording issues in documentation

### Changes

- minor changes in package dependencies (composer.json)
- remove obsolete psr-4 autoload nodes from composer.json
- change support e-mail in composer.json


## [1.0.0], 2015-11-01:
_first stable release_

### Fixed

- Fixed memory leak issue in file scan processor
- Fixed Documentation wording and meta issues
- Fixed phpmd/phpcs issues in some base classes
- Fixed output level 0 information problem
- Fixed scan output for class as function
- Fixed exclude path problem
- Fixed functional tests

### Changes

- refactor option and argument base in cli
- extend help meta in cli for <scan> command
- extend execution title by used output level
- remove additional warning fragments in code base
- remove time measurement properties and methods
- minor improvements in cli functional testing
- travis ci build process improvements


## [0.9.9-beta3], 2015-10-17:
_feature freeze_

### Fixed

- Fixed autoload issue under composer related environment usage
- Fixed minor php7 compatibility issue
- Fixed minor output template issues
- Fixed method naming issue(s)

### Added

- Added new phpunit base class identifier
- Added travis ci build processor for php7

### Changes

- remove warning feature (will be re-introduced in future 1.0.1 stable version)
- improvements in class/method identification process, we'll scan only real php unit test classes now
- improvements in scanning speed


## [0.9.9-beta2], 2015-09-02:

### Fixed

- Fixed refactoring related error in output (commandline) unitTest
- Fixed baseValidation class failure halt-bug
- Fixed double function definition (removeExcludedPath)
- Fixed false positive failure validator match
- Fixed a php warning issue caused by missing use statements scan target classes
- Fixed property mismatch in coverFishHelper class

### Added

- Added stop-on-error option command

### Changes

- move xml related helper methods to additional (new) xml helper class
- extend coverFishHelper class, refactoring coverFish scanner class
- additional refactoring of baseCoverFishScanner and baseCoverFishResult classes
- minor/major scrutinizer/codeClimate related changes in project code
- minor documentation / output wording issues


## [0.9.9-beta1], 2015-08-27:

### Fixed

- Fixed issue in json result output
- Fixed constant usage problem and finish base refactoring of output class
- Fixed an unitTest commandline check issue

### Added

- Added stop-on-failure option command functionality
- Added validator for global code coverage using @cover in class head annotation
- Added new unit tests for global code coverage validator
- Added new unit tests for coverage warnings

### Changes

- minor changes in method naming
- minor refactoring of coverFishHelper class
- additional refactoring improvements, object property changes


## [0.9.8], 2015-08-19:

### Fixed

- Fixed problem with error return code on scan failures
- Fixed non-property bug in "phpunit scan mode" during phpunit.xml check
- Fixed error in output template usage for output-level 1+

### Added

- Added missing unit tests for collection/arrayCollection and other functional classes
- Added new console commandLine tests for "phpunit scan mode"
- Added new console commandLine tests for "raw scan mode"

### Changes

- minor readme/documentation issues
- improve code quality / fix internal review related issues
- improve output template usage, minor output format extend 
- refactoring of current test-base (validator/scanner tests)
- initial refactoring of our ugly output module (base class)


## [0.9.7], 2015-08-15:

### Fixed

- Fixed some errors in used method access types in baseScanner class

### Added

- Added phpunit.xml config file argument input support `--phpunit-config`
- Added phpunit.xml test suite select parameter input support `--phpunit-config-suite`
- Added additional unit tests for coverFishHelper and (base) coverFishScanner class
- Added raw-mode arguments/parameter

### Changes

- prefix current autoload, scan-path and exclude path argument/options in raw mode parameter
- improve documentation


## [0.9.6], 2015-08-12:

### Fixed

- Fixed usage of real error constants instead of integer values in CoverFishScannerValidatorTest
- Fixed some scrutinizer issues

### Added

- Added minor phpunit.xml input support as parameter `--phpunit-config`
- Added additional UnitTests to improve codeCoverage
- Added coverfish self-scan to available unitTests
- Added blind exception throw on any kind of scan failure

### Fixed

- Fixed color output problem in progress echo
- Fixed travis issue, disable color out on coverfish self scan
- Fixed travis issue, enable exit code based command chain

### Changes

- phpDoc additions
- minor documentation issues
- minor phpDoc issues


## [0.9.5], 2015-08-10:
_first community preview release_

### Fixed

- Fixed several major/minor scrutinizer issues, improve stability and code quality
- Fixed ArrayCollection handling of delete functionality
- Fixed minor OutPut module issue(s)

### Added

- Added scrutinizer badge
- Added code coverage process
- Added build script features for travis and scrutinizer


## [0.9.4], 2015-08-09:
_first official release_

### Fixed

- Fixed naming issue in all our base classes
- Fixed minor phpdoc/phpcs issues

### Added

- Added "no cover found" progress char ("N") in result output
- Added php code coverage annotation for all our tests
- Added `--exclude-path` option to exclude a specific path from planned scan 

### Changes

- code quality improvements
- documentation improvements
