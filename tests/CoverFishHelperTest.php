<?php

namespace DF\PHPCoverFish\Tests;

use DF\PHPCoverFish\CoverFishScanner;
use DF\PHPCoverFish\Tests\Base\BaseCoverFishScannerTestCase;

/**
 * Class CoverFishHelperTest
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.7
 * @version   0.9.7
 */
class CoverFishHelperTest extends BaseCoverFishScannerTestCase
{
    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkFileExists
     */
    public function testCheckFileExist()
    {
        $this->assertTrue($this->getCoverFishHelper()->checkFileExist(sprintf('%s/data/phpunit.xml', __DIR__)));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkParamNotEmpty
     */
    public function testCheckParamNotEmpty()
    {
        $this->assertTrue($this->getCoverFishHelper()->checkParamNotEmpty('foo'));
        $this->assertFalse($this->getCoverFishHelper()->checkParamNotEmpty(''));
        $this->assertFalse($this->getCoverFishHelper()->checkParamNotEmpty(null));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkPath
     */
    public function testCheckPathAndValidatePath()
    {
        $result = $this->getCoverFishHelper()->checkPath('tests/');

        $this->assertTrue('tests' === $this->getCoverFishHelper()->getLastItemInFQNBlock($result, '/'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkFileOrPath
     */
    public function testCheckFileOrPathAndValidateFileAndFile()
    {
        $this->assertTrue($this->getCoverFishHelper()->checkFileOrPath('tests/data/src/SampleClass.php'));
        $this->assertTrue($this->getCoverFishHelper()->checkFileOrPath('tests/data/src'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getClassNameFromClassFQN
     */
    public function testGetClassNameFromClassFullyQualifiedName()
    {
        $this->assertEquals('SampleClass', $this->getCoverFishHelper()->getClassNameFromClassFQN('DF\PHPCoverFish\Tests\Data\Src\SampleClass'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getFileNameFromPath
     */
    public function testGetFileNameFromPath()
    {
        $this->assertEquals('SampleClassOnlyPublicMethods.php', $this->getCoverFishHelper()->getFileNameFromPath('tests/data/src/SampleClassOnlyPublicMethods.php'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getLastItemInFQNBlock
     */
    public function testGetLastItemInFQNBlock()
    {
        $this->assertEquals('boo', $this->getCoverFishHelper()->getLastItemInFQNBlock('foo/make/boo', '/'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getUsedClassesInClass
     */
    public function testGetUsedClassesInClass()
    {
        $usedClassesInFile = $this->getCoverFishHelper()->getUsedClassesInClass(
            sprintf('%s/data/tests/ValidatorClassNameFailTest.php', __DIR__)
        );

        $this->assertGreaterThanOrEqual(1, count($usedClassesInFile));
        $this->assertTrue(in_array('DF\PHPCoverFish\Tests\Data\Src\SampleClass', $usedClassesInFile));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getFileContent
     */
    public function testGetFileContent()
    {
        $content = $this->getCoverFishHelper()->getFileContent(sprintf('%s/data/src/SampleClass.php', __DIR__));
        $this->assertGreaterThanOrEqual(1100, strlen($content));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getClassFromUse
     */
    public function testGetClassFromUse()
    {
        $usedClassesInFile = $this->getCoverFishHelper()->getUsedClassesInClass(
            sprintf('%s/data/tests/ValidatorClassNameFailTest.php', __DIR__)
        );

        $classFromUse = $this->getCoverFishHelper()->getClassFromUse('SampleClass', $usedClassesInFile);
        $this->assertEquals('DF\PHPCoverFish\Tests\Data\Src\SampleClass', $classFromUse);
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getCoversDefaultClassUsable
     */
    public function testGetCoversDefaultClassUsable()
    {
        $sampleCoverDefaultClasses = array(
            'DF\PHPCoverFish\Tests\Data\Src\SampleClassNoPrivateMethods',
            'DF\PHPCoverFish\Tests\Data\Src\SampleClassNoNotPublicMethods',
            'DF\PHPCoverFish\Tests\Data\Src\SampleClass'
        );

        $usableClassFromMultipleCoverDefaultClasses = $this->getCoverFishHelper()
            ->getCoversDefaultClassUsable($sampleCoverDefaultClasses);

        $this->assertEquals('DF\PHPCoverFish\Tests\Data\Src\SampleClass', $usableClassFromMultipleCoverDefaultClasses);
    }
}