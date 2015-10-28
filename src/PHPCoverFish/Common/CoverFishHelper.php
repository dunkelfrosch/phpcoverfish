<?php

namespace DF\PHPCoverFish\Common;

/**
 * Class CoverFishHelper, coverFish toolbox
 *
 * @package   DF\PHPCoverFish
 * @author    Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @copyright 2015 Patrick Paechnatz <patrick.paechnatz@gmail.com>
 * @license   http://www.opensource.org/licenses/MIT
 * @link      http://github.com/dunkelfrosch/phpcoverfish/tree
 * @since     class available since Release 0.9.0
 * @version   1.0.0
 */
class CoverFishHelper
{
    const PHPUNIT_ID_INTERFACE = 'PHPUnit_Framework_Test',
          PHPUNIT_ID_CLASS = 'PHPUnit_Framework_TestCase';

    /**
     * @param string $file
     *
     * @return bool
     */
    public function checkFileExist($file)
    {
        return (true === $this->checkParamNotEmpty($file) && true === file_exists($file));
    }

    /**
     * @param $param
     *
     * @return bool
     */
    public function checkParamNotEmpty($param)
    {
        return ((null !== $param) && ('' !== $param));
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    public function getClassNameFromClassFQN($namespace)
    {
        return $this->getLastItemInFQNBlock($namespace, '\\');
    }

    /**
     * @param string $fileNameAndPath
     *
     * @return string
     */
    public function getFileNameFromPath($fileNameAndPath)
    {
        return $this->getLastItemInFQNBlock($fileNameAndPath, '/');
    }

    /**
     * @param string $namespace
     *
     * @return string
     */
    public function getPathFromFileNameAndPath($namespace)
    {
        return (string) str_replace($this->getFileNameFromPath($namespace), null, $namespace);
    }

    /**
     * @param string $fqn
     * @param string $delimiter
     *
     * @return string
     */
    public function getLastItemInFQNBlock($fqn, $delimiter)
    {
        if (false === $fqnBlock = explode($delimiter, $fqn)) {
            return $fqnBlock;
        }

        return (string) $fqnBlock[count($fqnBlock) - 1];
    }

    /**
     * check for className in use statements, return className on missing use statement
     *
     * @param string     $coverClassName
     * @param array|null $usedClasses
     *
     * @return string
     */
    public function getClassFromUse($coverClassName, $usedClasses)
    {
        if (false === is_array($usedClasses)) {
            return $coverClassName;
        }

        foreach ($usedClasses as $use) {
            $this->getClassNameFromClassFQN($use);
            if ($coverClassName === $this->getClassNameFromClassFQN($use)) {
                return $use;
            }
        }

        return $coverClassName;
    }

    /**
     * return all in file use statement defined classes
     *
     * @param string $classFile absolute path of readable class file
     *
     * @return array
     */
    public function getUsedClassesInClass($classFile)
    {
        $useResult = array();
        $content = $this->getFileContent($classFile);
        if (preg_match_all('/(use\s+)(.*)(;)/', $content, $useResult) && 4 === count($useResult)) {
            // @todo: use keyName based result check instead of index!
            return ($useResult[2]);
        }

        return null;
    }

    /**
     * return loc of given test method
     *
     * @param array $methodData
     *
     * @return int
     */
    public function getLocOfTestMethod(array $methodData)
    {
        if (array_key_exists('endLine', $methodData) && array_key_exists('startLine', $methodData)) {
            return $methodData['endLine'] - $methodData['startLine'];
        }

        return 0;
    }

    /**
     * @param string $path
     *
     * @return string|false
     */
    public function checkPath($path)
    {
        $path = realpath($path);

        return ($path !== false && is_dir($path)) ? $path : false;
    }

    /**
     * @param string $fileOrPath
     *
     * @return bool
     */
    public function checkFileOrPath($fileOrPath)
    {
        if (false === $this->checkPath($fileOrPath)) {
            return file_exists($fileOrPath);
        }

        return true;
    }

    /**
     * @param string $file absolute path of readable class file
     *
     * @return null|string
     */
    public function getFileContent($file)
    {
        if (!is_readable($file)) {
            return null;
        }

        return file_get_contents($file);
    }

    /**
     * in case of (wrong) multiple annotation, get the last defined coversDefaultClass from class docBlock
     *
     * @param array $coversDefaultClass
     *
     * @return string
     */
    public function getCoversDefaultClassUsable(array $coversDefaultClass)
    {
        if (true === empty($coversDefaultClass)) {
            return null;
        }

        return $coversDefaultClass[count($coversDefaultClass) - 1];
    }

    /**
     * fetch annotation key value(s) as array from corresponding class docBlock directly
     *
     * @param string $docBlock
     * @param string $key
     *
     * @return array
     */
    public function getAnnotationByKey($docBlock, $key)
    {
        /** @var array $classAnnotations */
        $classAnnotations = $this->parseCoverAnnotationDocBlock($docBlock);
        if (false === array_key_exists($key, $classAnnotations)) {
            return array();
        }

        return $classAnnotations[$key];
    }

    /**
     * @param string $key
     * @param array  $classData
     *
     * @return string
     */
    public function getAttributeByKey($key, array $classData)
    {
        if (false === array_key_exists($key, $classData)) {
            return '';
        }

        return (string) $classData[$key];
    }

    /**
     * @param string $inputPath
     *
     * @return string
     */
    public function getRegexPath($inputPath)
    {
        return sprintf('/%s/', str_replace('/', '\/', $inputPath));
    }

    /**
     * @param string $docBlock
     *
     * @return array
     */
    public function parseCoverAnnotationDocBlock($docBlock)
    {
        $annotations = array('covers' => array(), 'uses' => array());
        $docBlock = substr($docBlock, 3, -2);
        if (preg_match_all('/@(?P<name>[A-Za-z_-]+)(?:[ \t]+(?P<value>.*?))?[ \t]*\r?$/m', $docBlock, $matches)) {
            $numMatches = count($matches[0]);
            for ($i = 0; $i < $numMatches; ++$i) {
                $annotations[$matches['name'][$i]][] = $matches['value'][$i];
            }
        }
        array_walk_recursive(
            $annotations,
            function(&$element) {
                if (substr($element, 0, 1) === '\\') {
                    $element = substr($element, 1);
                }
            }
        );
        return $annotations;
    }

    public function isValidTestMethod($methodSignature)
    {
        $result = array();

        if (preg_match_all('/(?P<prefix>^test)/', $methodSignature, $result)
            && (array_key_exists('prefix', $result) && false === empty($result['prefix']))) {
            return true;
        }

        return false;
    }

    /**
     * Check if a class extends or implements a specific class/interface
     *
     * @param string $classFQN The class name of the object to compare to
     *
     * @return bool
     */
    public function isValidTestClass($classFQN)
    {
        try {
            $testClass = new \ReflectionClass($classFQN);
            do {
                if( self::PHPUNIT_ID_CLASS === $testClass->getName() ) {
                    return true;
                }

                $interfaces = $testClass->getInterfaceNames();
                if (is_array( $interfaces) && in_array(self::PHPUNIT_ID_INTERFACE, $interfaces)) {
                    return true;
                }

                $testClass = $testClass->getParentClass();

            } while (false !== $testClass);

        } catch (\Exception $e) {
            return false;
        }

        return false;
    }

    /**
     * check if class got fully qualified name
     *
     * @param string $class
     *
     * @return bool
     */
    public function checkClassHasFQN($class)
    {
        preg_match_all('/(\\\\+)/', $class, $result, PREG_SET_ORDER);

        return count($result) > 0;
    }

    /**
     * @param $string
     *
     * @return bool
     */
    public function stringToBool($string)
    {
        return filter_var($string, FILTER_VALIDATE_BOOLEAN);
    }
}