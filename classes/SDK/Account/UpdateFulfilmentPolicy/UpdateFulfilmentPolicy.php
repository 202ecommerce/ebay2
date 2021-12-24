<?php
namespace Ebay\classes\SDK\Account\UpdateFulfilmentPolicy;

use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\CreateFulfilmentPolicy;
use Ebay\classes\SDK\Account\UpdateFulfilmentPolicy\Request as UpdateRequest;
use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Response as GetResponse;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;
use Ebay\services\Builder\BuilderInterface;
use Ebay\services\Builder\FulfilmentBuilder;
use Symfony\Component\VarDumper\VarDumper;
use Exception;

class UpdateFulfilmentPolicy extends CreateFulfilmentPolicy
{
    protected function getRequest()
    {
        $fulfilment = $this->getFulfilmentBuilder()->build();
        return new UpdateRequest(
            $this->getToken(),
            $fulfilment
        );
    }
}
