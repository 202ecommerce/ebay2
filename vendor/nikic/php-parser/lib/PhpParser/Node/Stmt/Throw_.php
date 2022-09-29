<?php

namespace EbayVendor\PhpParser\Node\Stmt;

use EbayVendor\PhpParser\Node;
class Throw_ extends Node\Stmt
{
    /** @var Node\Expr Expression */
    public $expr;
    /**
     * Constructs a throw node.
     *
     * @param Node\Expr $expr       Expression
     * @param array     $attributes Additional attributes
     */
    public function __construct(Node\Expr $expr, array $attributes = array())
    {
        parent::__construct($attributes);
        $this->expr = $expr;
    }
    public function getSubNodeNames()
    {
        return array('expr');
    }
}
