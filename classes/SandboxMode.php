<?php
namespace Ebay\classes;

class SandboxMode
{
    protected $sandboxMode;

    public function __construct($sandboxMode = null)
    {
        $this->setSandboxMode($sandboxMode);
    }

    public function isSandbox()
    {
        if (is_null($this->sandboxMode)) {
            return defined('EBAY_DEV') ? EBAY_DEV : false;
        }

        return (bool)$this->sandboxMode;
    }

    public function setSandboxMode($sandboxMode)
    {
        $this->sandboxMode = $sandboxMode;
    }
}
