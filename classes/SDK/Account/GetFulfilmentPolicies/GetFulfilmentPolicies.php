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

namespace Ebay\classes\SDK\Account\GetFulfilmentPolicies;

use Ebay\classes\SDK\Account\GetFulfilmentPolicies\Request as GetRequest;
use Ebay\classes\SDK\Account\GetFulfilmentPolicies\Response as GetResponse;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;
use Ebay\services\Marketplace;
use Exception;

class GetFulfilmentPolicies
{
    /** @var \EbayProfile */
    protected $ebayProfile;

    public function __construct(\EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;
    }

    /**
     * @return EbayApiResponse
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

        $resultContent = json_decode($result->getBody()->getContents(), true);
        $response = (new GetResponse())
            ->setSuccess(true)
            ->setResult($result);

        if (isset($resultContent['fulfillmentPolicies']) && false == empty($resultContent['fulfillmentPolicies'])) {
            $response->fulfilmentPolicies = (new FulfilmentPolicyList())->fromArray($resultContent['fulfillmentPolicies']);
        }

        return $response;
    }

    protected function getToken()
    {
        return new BearerAuthToken($this->ebayProfile);
    }

    /**
     * @return string
     */
    protected function getMarketplace()
    {
        return (new Marketplace())->getByProfile($this->ebayProfile);
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
        return new GetRequest(
            $this->getToken(),
            $this->getMarketplace()
        );
    }
}
