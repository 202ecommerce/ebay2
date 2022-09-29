<?php

namespace EbayVendor\PhpParser\Node\Stmt;

use EbayVendor\PhpParser\Node;
/** Nop/empty statement (;). */
class Nop extends Node\Stmt
{
    public function getSubNodeNames()
    {
        return array();
    }
}
