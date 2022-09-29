<?php

namespace EbayVendor\PhpParser\Parser;

use EbayVendor\PhpParser\Lexer;
use EbayVendor\PhpParser\ParserTest;
require_once __DIR__ . '/../ParserTest.php';
class Php5Test extends ParserTest
{
    protected function getParser(Lexer $lexer)
    {
        return new Php5($lexer);
    }
}
