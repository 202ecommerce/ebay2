<?php
namespace Ebay\classes\SDK\Account\GetFulfilmentPolicies;

use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;

class Request extends AbstractBearerRequest
{
    /** @var string*/
    protected $marketplace;

    /**
     * @param BearerAuthToken $token
     * @param string $marketplace
     */
    public function __construct(BearerAuthToken $token, $marketplace)
    {
        parent::__construct($token);
        $this->marketplace = (string)$marketplace;
    }

    /** @return string */
    public function getEndPoint()
    {
        return '/sell/account/v1/fulfillment_policy?marketplace_id=' . $this->marketplace;
    }

    /** @return string */
    public function toJson()
    {
        return '';
    }

    /** @return array */
    public function toArray()
    {
        return [];
    }

    /** @return string */
    public function getMethod()
    {
        return 'get';
    }
}