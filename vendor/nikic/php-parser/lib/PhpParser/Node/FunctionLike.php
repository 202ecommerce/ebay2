<?php

namespace EbayVendor\PhpParser\Node;

use EbayVendor\PhpParser\Node;
interface FunctionLike extends Node
{
    /**
     * Whether to return by reference
     *
     * @return bool
     */
    public function returnsByRef();
    /**
     * List of parameters
     *
     * @return Node\Param[]
     */
    public function getParams();
    /**
     * Get the declared return type or null
     * 
     * @return null|string|Node\Name|Node\NullableType
     */
    public function getReturnType();
    /**
     * The function body
     *
     * @return Node\Stmt[]
     */
    public function getStmts();
}
