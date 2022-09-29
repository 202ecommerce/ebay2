<?php

namespace EbayVendor\PhpParser\Node\Stmt;

use EbayVendor\PhpParser\Node;
abstract class TraitUseAdaptation extends Node\Stmt
{
    /** @var Node\Name Trait name */
    public $trait;
    /** @var string Method name */
    public $method;
}
