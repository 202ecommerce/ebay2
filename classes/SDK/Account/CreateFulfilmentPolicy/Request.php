<?php
namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use GuzzleHttp\RequestOptions;

class Request extends AbstractBearerRequest
{
    /** @return FulfilmentPolicy */
    protected $fulfilmentPolicy;

    public function __construct(BearerAuthToken $token, FulfilmentPolicy $fulfilmentPolicy)
    {
        parent::__construct($token);
        $this->setFulfilmentPolicy($fulfilmentPolicy);
    }

    /** @return string */
    public function getEndPoint()
    {
        return '/sell/account/v1/fulfillment_policy';
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
        return 'post';
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options[RequestOptions::BODY] = $this->fulfilmentPolicy->toJson();

        return $options;
    }

    /**
     * @param FulfilmentPolicy
     * @return self
     */
    public function setFulfilmentPolicy(FulfilmentPolicy $fulfilmentPolicy)
    {
        $this->fulfilmentPolicy = $fulfilmentPolicy;
        return $this;
    }
}