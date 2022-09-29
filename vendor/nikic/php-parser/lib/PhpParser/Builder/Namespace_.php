<?php

namespace EbayVendor\PhpParser\Builder;

use EbayVendor\PhpParser;
use EbayVendor\PhpParser\Node;
use EbayVendor\PhpParser\Node\Stmt;
class Namespace_ extends Declaration
{
    private $name;
    private $stmts = array();
    /**
     * Creates a namespace builder.
     *
     * @param Node\Name|string|null $name Name of the namespace
     */
    public function __construct($name)
    {
        $this->name = null !== $name ? $this->normalizeName($name) : null;
    }
    /**
     * Adds a statement.
     *
     * @param Node|PhpParser\Builder $stmt The statement to add
     *
     * @return $this The builder instance (for fluid interface)
     */
    public function addStmt($stmt)
    {
        $this->stmts[] = $this->normalizeNode($stmt);
        return $this;
    }
    /**
     * Returns the built node.
     *
     * @return Node The built node
     */
    public function getNode()
    {
        return new Stmt\Namespace_($this->name, $this->stmts, $this->attributes);
    }
}
