<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishScannerTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   0.9.4
 */
class CoverFishScannerTest extends BaseCoverFishScannerTestCase
{
    /**
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::scanFilesInPath
     */
    public function testScanFilesInPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(null, null),
            $this->getDefaultOutputOptions()
        );

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data/src', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkPath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::getRegexPath
     * @covers DF\PHPCoverFish\Base\BaseCoverFishScanner::removeExcludedPath
     */
    public function testScanFilesAndIgnoreExcludedPath()
    {
        /** @var CoverFishScanner $scanner */
        $scanner = new CoverFishScanner(
            $this->getDefaultCLIOptions(null, 'tests/data/tests'),
            $this->getDefaultOutputOptions()
        );

        /** @var array $files */
        $files = $scanner->scanFilesInPath(sprintf('%s/data', __DIR__));

        $this->assertGreaterThanOrEqual(8, count($files));
        $this->assertTrue($this->validateTestsDataSrcFixturePathContent($files));
    }
}