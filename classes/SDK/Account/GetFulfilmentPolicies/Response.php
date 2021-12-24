<?php
namespace Ebay\classes\SDK\Account\GetFulfilmentPolicies;

use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;

class Response extends EbayApiResponse
{
    /** @var FulfilmentPolicyList */
    public $fulfilmentPolicies;

    public function __construct()
    {
        $this->fulfilmentPolicies = new FulfilmentPolicyList();
    }
}
