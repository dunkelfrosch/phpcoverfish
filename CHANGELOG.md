# Changes in PHPCoverFish 0.9.6

All notable changes of the PHPCoverFish release series are documented in this file using the [Keep a CHANGELOG](http://keepachangelog.com/) principles.
This PHPCoverFish change-log documentation start with version 0.9.4 (2015-08-09).

## [0.9.6] - 2015-08-12

### Fixed

- Fixed usage of real error constants instead of integer values in CoverFishScannerValidatorTest
- Fixed some scrutinizer issues

### Added

- Added phpunit.xml direct input support as parameter `--phpunit-config`
- Added additional UnitTests to improve codeCoverage
- Added coverfish self-scan to available unitTests
- Added blind exception throw on any kind of scan failure

### Changes

- phpDoc additions
- minor documentation issues
- minor phpDoc issues

## [0.9.5] - 2015-08-10, 2015-08-11

### Fixed

- Fixed several major/minor scrutinizer issues, improve stability and code quality
- Fixed ArrayCollection handling of delete functionality
- Fixed minor OutPut module issue(s)

### Added

- Added scrutinizer badge
- Added code coverage process
- Added build script features for travis and scrutinizer


## [0.9.4] - 2015-08-09

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
