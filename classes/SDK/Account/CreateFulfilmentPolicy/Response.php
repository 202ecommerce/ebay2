<?php
namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;

class Response extends EbayApiResponse
{
    /** @var FulfilmentPolicy*/
    protected $fulfilmentPolicy = null;

    public function setFulfilmentPolicy(FulfilmentPolicy $fulfilmentPolicy)
    {
        $this->fulfilmentPolicy = $fulfilmentPolicy;
    }

    public function getFulfilmentPolicy()
    {
        if (is_null($this->fulfilmentPolicy)) {
            return new FulfilmentPolicy();
        }

        return $this->fulfilmentPolicy;
    }
}
