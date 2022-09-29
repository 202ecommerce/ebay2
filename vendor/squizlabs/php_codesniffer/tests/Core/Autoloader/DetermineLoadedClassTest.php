<?php

/**
 * Tests for the \PHP_CodeSniffer\Util\Common::isCamelCaps method.
 *
 * @author    Greg Sherwood <gsherwood@squiz.net>
 * @copyright 2006-2015 Squiz Pty Ltd (ABN 77 084 670 600)
 * @license   https://github.com/squizlabs/PHP_CodeSniffer/blob/master/licence.txt BSD Licence
 */
namespace EbayVendor\PHP_CodeSniffer\Tests\Core\Autoloader;

use EbayVendor\PHPUnit\Framework\TestCase;
class DetermineLoadedClassTest extends TestCase
{
    /**
     * Load the test files.
     *
     * @return void
     */
    public static function setUpBeforeClass()
    {
        include __DIR__ . '/TestFiles/Sub/C.inc';
    }
    //end setUpBeforeClass()
    /**
     * Test for when class list is ordered.
     *
     * @return void
     */
    public function testOrdered()
    {
        $classesBeforeLoad = ['classes' => [], 'interfaces' => [], 'traits' => []];
        $classesAfterLoad = ['classes' => ['EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\A', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\B', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C'], 'interfaces' => [], 'traits' => []];
        $className = \EbayVendor\PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', $className);
    }
    //end testOrdered()
    /**
     * Test for when class list is out of order.
     *
     * @return void
     */
    public function testUnordered()
    {
        $classesBeforeLoad = ['classes' => [], 'interfaces' => [], 'traits' => []];
        $classesAfterLoad = ['classes' => ['EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\A', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\B'], 'interfaces' => [], 'traits' => []];
        $className = \EbayVendor\PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', $className);
        $classesAfterLoad = ['classes' => ['EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\A', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\B'], 'interfaces' => [], 'traits' => []];
        $className = \EbayVendor\PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', $className);
        $classesAfterLoad = ['classes' => ['EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\A', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\C', 'EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\B'], 'interfaces' => [], 'traits' => []];
        $className = \EbayVendor\PHP_CodeSniffer\Autoload::determineLoadedClass($classesBeforeLoad, $classesAfterLoad);
        $this->assertEquals('EbayVendor\\PHP_CodeSniffer\\Tests\\Core\\Autoloader\\Sub\\C', $className);
    }
    //end testUnordered()
}
//end class
