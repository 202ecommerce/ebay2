<?php
namespace Ebay\classes\SDK\Core;

use Ebay\classes\SandboxMode;

class ApiBaseUri implements ApiBaseUriInterface
{
    /** @var SandboxMode*/
    protected $sandboxMode;

    public function __construct($mode = null)
    {
        if (false == $mode instanceof SandboxMode) {
            $mode = new SandboxMode();
        }

        $this->sandboxMode = $mode;
    }

    public function get()
    {
        if ($this->sandboxMode->isSandbox()) {
            return 'https://api.sandbox.ebay.com';
        }

        return 'https://api.ebay.com';
    }
}
