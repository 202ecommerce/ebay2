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

namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use EbayVendor\GuzzleHttp\RequestOptions;

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
    public function getMethod()
    {
        return 'post';
    }

    public function getOptions()
    {
        $options = parent::getOptions();
        $options[RequestOptions::BODY] = $this->fulfilmentPolicy->toJson();
        $options[RequestOptions::HEADERS]['Content-Type'] = 'application/json';

        return $options;
    }

    /**
     * @param FulfilmentPolicy
     *
     * @return self
     */
    public function setFulfilmentPolicy(FulfilmentPolicy $fulfilmentPolicy)
    {
        $this->fulfilmentPolicy = $fulfilmentPolicy;

        return $this;
    }
}
