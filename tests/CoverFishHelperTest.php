<?php

namespace DF\PHPCoverFish\Tests;

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
 * @version   0.9.9
 */
class CoverFishHelperTest extends BaseCoverFishScannerTestCase
{
    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkClassHasFQN
     */
    public function testCheckClassHasFQN()
    {
        $this->assertTrue($this->getCoverFishHelper()->checkClassHasFQN('DF\PHPCoverFish\Tests\Data\Src\SampleClass'));
        $this->assertFalse($this->getCoverFishHelper()->checkClassHasFQN('SampleClass'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getRegexPath
     */
    public function testCheckGetRegexPath()
    {
        $resultPath = $this->getCoverFishHelper()->getRegexPath('tests/data/exclude');
        $this->assertEquals('/tests\/data\/exclude/', $resultPath);
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getPathFromFileNameAndPath
     */
    public function testCheckGetPathFromFileNameAndPath()
    {
        $resultPath = $this->getCoverFishHelper()->getPathFromFileNameAndPath('tests/data/src/SampleClass.php');
        $this->assertEquals('tests/data/src/', $resultPath);
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::parseCoverAnnotationDocBlock
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getAnnotationByKey
     */
    public function testCheckParseCoverAnnotationDocBlock()
    {
        $classData = $this->getSampleClassData(sprintf('%s/data/tests/ValidatorDefaultCoverClassPassTest.php', __DIR__));

        $annotationCheck = $this->getCoverFishHelper()->getAnnotationByKey($classData['docblock'], 'coversDefaultClass');
        $this->assertTrue(is_array($annotationCheck));
        $this->assertEquals($annotationCheck, array('DF\PHPCoverFish\Tests\Data\Src\SampleClass'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getAttributeByKey
     *
     */
    public function testCheckGetAttributeByKey()
    {
        $testFile = 'ValidatorGlobalMethodPassTest.php';
        $classData = $this->getSampleClassData(sprintf('%s/data/tests/%s', __DIR__, $testFile));

        $namespaceCheck = $this->getCoverFishHelper()->getAttributeByKey('namespace', $classData['package']);
        $fileCheck = $this->getCoverFishHelper()->getAttributeByKey('file', $classData);

        $this->assertEquals('DF\PHPCoverFish\Tests\Data\Tests', $namespaceCheck);
        $this->assertEquals($testFile, $this->getCoverFishHelper()->getFileNameFromPath($fileCheck));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getLocOfTestMethod
     */
    public function testCheckGetLocOfTestMethod()
    {
        $testFile = 'ValidatorGlobalMethodPassTest.php';
        $methodData = $this->getSampleClassMethodData(sprintf('%s/data/tests/%s', __DIR__, $testFile));
        $loc = $this->getCoverFishHelper()->getLocOfTestMethod($methodData);

        $this->assertEquals(4, $loc);
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::checkFileExist
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
    public function testCheckGetClassNameFromClassFullyQualifiedName()
    {
        $this->assertEquals('SampleClass', $this->getCoverFishHelper()->getClassNameFromClassFQN('DF\PHPCoverFish\Tests\Data\Src\SampleClass'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getFileNameFromPath
     */
    public function testCheckGetFileNameFromPath()
    {
        $this->assertEquals('SampleClassOnlyPublicMethods.php', $this->getCoverFishHelper()->getFileNameFromPath('tests/data/src/SampleClassOnlyPublicMethods.php'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getLastItemInFQNBlock
     */
    public function testCheckGetLastItemInFQNBlock()
    {
        $this->assertEquals('boo', $this->getCoverFishHelper()->getLastItemInFQNBlock('foo/make/boo', '/'));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getUsedClassesInClass
     */
    public function testCheckGetUsedClassesInClass()
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
    public function testCheckGetFileContent()
    {
        $content = $this->getCoverFishHelper()->getFileContent(sprintf('%s/data/src/SampleClass.php', __DIR__));
        $this->assertGreaterThanOrEqual(1100, strlen($content));
    }

    /**
     * @covers DF\PHPCoverFish\Common\CoverFishHelper::getClassFromUse
     */
    public function testCheckGetClassFromUse()
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
    public function testCheckGetCoversDefaultClassUsable()
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