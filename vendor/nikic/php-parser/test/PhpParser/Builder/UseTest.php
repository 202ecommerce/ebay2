<?php

namespace EbayVendor;

use EbayVendor\PhpParser\Builder;
use EbayVendor\PhpParser\Node\Name;
use EbayVendor\PhpParser\Node\Stmt;
class UseTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    protected function createUseBuilder($name, $type = Stmt\Use_::TYPE_NORMAL)
    {
        return new Builder\Use_($name, $type);
    }
    public function testCreation()
    {
        $node = $this->createUseBuilder('EbayVendor\\Foo\\Bar')->getNode();
        $this->assertEquals(new Stmt\Use_(array(new Stmt\UseUse(new Name('EbayVendor\\Foo\\Bar'), 'Bar'))), $node);
        $node = $this->createUseBuilder(new Name('EbayVendor\\Foo\\Bar'))->as('XYZ')->getNode();
        $this->assertEquals(new Stmt\Use_(array(new Stmt\UseUse(new Name('EbayVendor\\Foo\\Bar'), 'XYZ'))), $node);
        $node = $this->createUseBuilder('EbayVendor\\foo\\bar', Stmt\Use_::TYPE_FUNCTION)->as('foo')->getNode();
        $this->assertEquals(new Stmt\Use_(array(new Stmt\UseUse(new Name('EbayVendor\\foo\\bar'), 'foo')), Stmt\Use_::TYPE_FUNCTION), $node);
    }
    public function testNonExistingMethod()
    {
        $this->setExpectedException('LogicException', 'Method "foo" does not exist');
        $builder = $this->createUseBuilder('Test');
        $builder->foo();
    }
}
\class_alias('EbayVendor\\UseTest', 'UseTest', \false);
