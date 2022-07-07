<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory;

use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\ApiBaseUriInterface;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Core\RequestInterface;
use Ebay\classes\SDK\Lib\AspectList;
use Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory\Request as GetRequest;
use Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory\Response as GetResponse;
use Ebay\services\CategoryTree;
use EbayProfile;
use Exception;

class GetItemAspectsForCategory
{
    /** @var EbayProfile */
    protected $ebayProfile;

    /** @var string */
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
     *
     * @return self
     */
    public function setEbayProfile(EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;

        return $this;
    }

    /**
     * @param string $categoryId
     *
     * @return self
     */
    public function setCategoryId($categoryId)
    {
        $this->categoryId = (string) $categoryId;

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
        return new BearerAuthToken($this->ebayProfile);
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
