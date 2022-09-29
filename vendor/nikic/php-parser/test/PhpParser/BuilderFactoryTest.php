<?php

namespace EbayVendor\PhpParser;

use EbayVendor\PhpParser\Node\Expr;
class BuilderFactoryTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    /**
     * @dataProvider provideTestFactory
     */
    public function testFactory($methodName, $className)
    {
        $factory = new BuilderFactory();
        $this->assertInstanceOf($className, $factory->{$methodName}('test'));
    }
    public function provideTestFactory()
    {
        return array(array('namespace', 'EbayVendor\\PhpParser\\Builder\\Namespace_'), array('class', 'EbayVendor\\PhpParser\\Builder\\Class_'), array('interface', 'EbayVendor\\PhpParser\\Builder\\Interface_'), array('trait', 'EbayVendor\\PhpParser\\Builder\\Trait_'), array('method', 'EbayVendor\\PhpParser\\Builder\\Method'), array('function', 'EbayVendor\\PhpParser\\Builder\\Function_'), array('property', 'EbayVendor\\PhpParser\\Builder\\Property'), array('param', 'EbayVendor\\PhpParser\\Builder\\Param'), array('use', 'EbayVendor\\PhpParser\\Builder\\Use_'));
    }
    public function testNonExistingMethod()
    {
        $this->setExpectedException('LogicException', 'Method "foo" does not exist');
        $factory = new BuilderFactory();
        $factory->foo();
    }
    public function testIntegration()
    {
        $factory = new BuilderFactory();
        $node = $factory->namespace('EbayVendor\\Name\\Space')->addStmt($factory->use('EbayVendor\\Foo\\Bar\\SomeOtherClass'))->addStmt($factory->use('EbayVendor\\Foo\\Bar')->as('A'))->addStmt($factory->class('SomeClass')->extend('SomeOtherClass')->implement('EbayVendor\\A\\Few', '\\Interfaces')->makeAbstract()->addStmt($factory->method('firstMethod'))->addStmt($factory->method('someMethod')->makePublic()->makeAbstract()->addParam($factory->param('someParam')->setTypeHint('SomeClass'))->setDocComment('/**
                                      * This method does something.
                                      *
                                      * @param SomeClass And takes a parameter
                                      */'))->addStmt($factory->method('anotherMethod')->makeProtected()->addParam($factory->param('someParam')->setDefault('test'))->addStmt(new Expr\Print_(new Expr\Variable('someParam'))))->addStmt($factory->property('someProperty')->makeProtected())->addStmt($factory->property('anotherProperty')->makePrivate()->setDefault(array(1, 2, 3))))->getNode();
        $expected = <<<'EOC'
<?php

namespace EbayVendor\Name\Space;

use EbayVendor\Foo\Bar\SomeOtherClass;
use EbayVendor\Foo\Bar as A;
abstract class SomeClass extends SomeOtherClass implements A\Few, \EbayVendor\Interfaces
{
    protected $someProperty;
    private $anotherProperty = array(1, 2, 3);
    function firstMethod()
    {
    }
    /**
     * This method does something.
     *
     * @param SomeClass And takes a parameter
     */
    public abstract function someMethod(SomeClass $someParam);
    protected function anotherMethod($someParam = 'test')
    {
        print $someParam;
    }
}
EOC;
        $stmts = array($node);
        $prettyPrinter = new PrettyPrinter\Standard();
        $generated = $prettyPrinter->prettyPrintFile($stmts);
        $this->assertEquals(\str_replace("\r\n", "\n", $expected), \str_replace("\r\n", "\n", $generated));
    }
}
