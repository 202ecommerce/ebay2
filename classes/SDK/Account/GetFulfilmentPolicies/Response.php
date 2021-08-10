<?php


namespace Ebay\classes\SDK\Account\GetFulfilmentPolicies;


use Ebay\classes\SDK\Core\EbayApiResponse;

class Response extends EbayApiResponse
{
    /** @array */
    public $fulfilmentPolicies = [];
}