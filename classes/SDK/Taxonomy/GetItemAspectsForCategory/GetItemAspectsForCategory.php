<?php


namespace Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory;


use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Lib\AspectList;
use Ebay\services\CategoryTree;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\ApiBaseUriInterface;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Core\RequestInterface;
use Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory\Request as GetRequest;
use Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory\Response as GetResponse;
use EbayProfile;
use Exception;
use Symfony\Component\VarDumper\VarDumper;

class GetItemAspectsForCategory
{
    /** @var EbayProfile*/
    protected $ebayProfile;

    /** @var string*/
    protected $categoryId;

    /**
     * @param EbayProfile $ebayProfile
     * @param string $categoryId
     */
    public function __construct(EbayProfile $ebayProfile, $categoryId)
    {
        $this->setEbayProfile($ebayProfile);
        $this->setCategoryId($categoryId);
    }

    /**
     * @param EbayProfile $ebayProfile
     * @return self
     */
    public function setEbayProfile(EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;
        return $this;
    }

    /**
     * @param string $categoryId
     * @return self
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (string)$categoryId;
        return $this;
    }

    /**
     * @return GetResponse
     */
    public function execute()
    {
        $response = $this->getResponse();
        try {
            $apiResponse = $this->getClient()->executeRequest($this->getRequest());
        } catch (Exception $e) {
            return $response->setSuccess(false);
        }

        if ($apiResponse->getStatusCode() != 200) {
            return $response->setSuccess(false)->setResult($response);
        }

        $resultContent = json_decode($apiResponse->getBody()->getContents(), true);
        return $response
            ->setAspectList(
                (new AspectList())->fromArray($resultContent)
            )
            ->setResult($apiResponse)
            ->setSuccess(true);

    }

    /**
     * @return EbayClient
     */
    protected function getClient()
    {
        return new EbayClient($this->getApiBaseUri());
    }

    /**
     * @return ApiBaseUriInterface
     */
    protected function getApiBaseUri()
    {
        return new ApiBaseUri();
    }

    /**
     * @return RequestInterface
     */
    protected function getRequest()
    {
        return new GetRequest(
            $this->getToken(),
            $this->getCategoryTreeId(),
            $this->getCategoryId()
        );
    }

    protected function getToken()
    {
        return (new BearerAuthToken($this->ebayProfile));
    }

    protected function getCategoryId()
    {
        return $this->categoryId;
    }

    protected function getCategoryTreeId()
    {
        return $this->getCategoryTreeService()->getIdByProfile($this->ebayProfile);
    }

    protected function getCategoryTreeService()
    {
        return new CategoryTree();
    }

    protected function getResponse()
    {
        return new GetResponse();
    }
}