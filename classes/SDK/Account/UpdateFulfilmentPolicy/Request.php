<?php
namespace Ebay\classes\SDK\Account\UpdateFulfilmentPolicy;

use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use EbayVendor\GuzzleHttp\RequestOptions;
use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Request as CreateRequest;

class Request extends CreateRequest
{
    /** @return string */
    public function getEndPoint()
    {
        return '/sell/account/v1/fulfillment_policy/' . (string)$this->fulfilmentPolicy->getFulfillmentPolicyId();
    }

    /** @return string */
    public function getMethod()
    {
        return 'put';
    }
}