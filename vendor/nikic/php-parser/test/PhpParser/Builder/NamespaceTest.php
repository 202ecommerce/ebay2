<?php

namespace EbayVendor\PhpParser\Builder;

use EbayVendor\PhpParser\Comment\Doc;
use EbayVendor\PhpParser\Node;
use EbayVendor\PhpParser\Node\Stmt;
class NamespaceTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    protected function createNamespaceBuilder($fqn)
    {
        return new Namespace_($fqn);
    }
    public function testCreation()
    {
        $stmt1 = new Stmt\Class_('SomeClass');
        $stmt2 = new Stmt\Interface_('SomeInterface');
        $stmt3 = new Stmt\Function_('someFunction');
        $docComment = new Doc('/** Test */');
        $expected = new Stmt\Namespace_(new Node\Name('EbayVendor\\Name\\Space'), array($stmt1, $stmt2, $stmt3), array('comments' => array($docComment)));
        $node = $this->createNamespaceBuilder('EbayVendor\\Name\\Space')->addStmt($stmt1)->addStmts(array($stmt2, $stmt3))->setDocComment($docComment)->getNode();
        $this->assertEquals($expected, $node);
        $node = $this->createNamespaceBuilder(new Node\Name(array('Name', 'Space')))->setDocComment($docComment)->addStmts(array($stmt1, $stmt2))->addStmt($stmt3)->getNode();
        $this->assertEquals($expected, $node);
        $node = $this->createNamespaceBuilder(null)->getNode();
        $this->assertNull($node->name);
        $this->assertEmpty($node->stmts);
    }
}
