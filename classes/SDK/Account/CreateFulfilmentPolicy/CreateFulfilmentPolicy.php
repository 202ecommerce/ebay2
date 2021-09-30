<?php


namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Request as GetRequest;
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

class CreateFulfilmentPolicy
{
    /** @var \EbayProfile*/
    protected $ebayProfile;

    /** @var mixed*/
    protected $data;

    public function __construct(\EbayProfile $ebayProfile, $data)
    {
        $this->ebayProfile = $ebayProfile;
        $this->setData($data);
    }

    /**
     * @return GetRequest
     */
    public function execute()
    {
        try {
            $result = $this->getClient()->executeRequest($this->getRequest());
        } catch (Exception $e) {
            return (new GetResponse())->setSuccess(false);
        }

        if ($result->getStatusCode() != 200) {
            return (new GetResponse())
                ->setSuccess(false)
                ->setResult($result);
        }

        $fulfilmentPolicy = (new FulfilmentPolicy())->fromJson($result->getBody()->getContents());
        $response = (new GetResponse())
            ->setSuccess(true)
            ->setResult($result)
            ->setFulfilmentPolicy($fulfilmentPolicy);

        return $response;
    }

    protected function getToken()
    {
        return (new BearerAuthToken($this->ebayProfile));
    }

    /**
     * @return EbayClient
     */
    protected function getClient()
    {
        return new EbayClient($this->getApiBaseUri());
    }

    protected function getApiBaseUri()
    {
        return new ApiBaseUri();
    }

    protected function getRequest()
    {
        $fulfilment = $this->getFulfilmentBuilder()->build();
        return new GetRequest(
            $this->getToken(),
            $fulfilment
        );
    }

    /**
     * @param mixed $data
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    /** @return BuilderInterface*/
    protected function getFulfilmentBuilder()
    {
        return new FulfilmentBuilder($this->data);
    }
}