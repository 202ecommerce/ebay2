<?php

namespace EbayVendor\PhpParser\Node\Expr;

use EbayVendor\PhpParser\Node\Expr;
class PreInc extends Expr
{
    /** @var Expr Variable */
    public $var;
    /**
     * Constructs a pre increment node.
     *
     * @param Expr  $var        Variable
     * @param array $attributes Additional attributes
     */
    public function __construct(Expr $var, array $attributes = array())
    {
        parent::__construct($attributes);
        $this->var = $var;
    }
    public function getSubNodeNames()
    {
        return array('var');
    }
}
