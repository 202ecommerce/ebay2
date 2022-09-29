<?php

namespace EbayVendor\PhpParser;

/* This test is very weak, because PHPUnit's assertEquals assertion is way too slow dealing with the
 * large objects involved here. So we just do some basic instanceof tests instead. */
class ParserFactoryTest extends \EbayVendor\PHPUnit_Framework_TestCase
{
    /** @dataProvider provideTestCreate */
    public function testCreate($kind, $lexer, $expected)
    {
        $this->assertInstanceOf($expected, (new ParserFactory())->create($kind, $lexer));
    }
    public function provideTestCreate()
    {
        $lexer = new Lexer();
        return [[ParserFactory::PREFER_PHP7, $lexer, 'EbayVendor\\PhpParser\\Parser\\Multiple'], [ParserFactory::PREFER_PHP5, null, 'EbayVendor\\PhpParser\\Parser\\Multiple'], [ParserFactory::ONLY_PHP7, null, 'EbayVendor\\PhpParser\\Parser\\Php7'], [ParserFactory::ONLY_PHP5, $lexer, 'EbayVendor\\PhpParser\\Parser\\Php5']];
    }
}
