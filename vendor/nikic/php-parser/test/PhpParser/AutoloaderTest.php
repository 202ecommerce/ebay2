<?php

namespace EbayVendor\PhpParser;

/* The autoloader is already active at this point, so we only check effects here. */
class AutoloaderTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    public function testClassExists()
    {
        $this->assertTrue(\class_exists('EbayVendor\\PhpParser\\NodeVisitorAbstract'));
        $this->assertFalse(\class_exists('EbayVendor\\PHPParser_NodeVisitor_NameResolver'));
        $this->assertFalse(\class_exists('EbayVendor\\PhpParser\\FooBar'));
        $this->assertFalse(\class_exists('EbayVendor\\PHPParser_FooBar'));
    }
}
