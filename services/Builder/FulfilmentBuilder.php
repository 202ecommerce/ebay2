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

namespace Ebay\services\Builder;

use Ebay\classes\SDK\Lib\CategoryType;
use Ebay\classes\SDK\Lib\CategoryTypeList;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\HandlingTime;
use Ebay\services\Marketplace;

class FulfilmentBuilder implements BuilderInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->setData($data);
    }

    public function build()
    {
        $fulfilmentPolicy = new FulfilmentPolicy();
        $fulfilmentPolicy->setCategoryTypes($this->getCategoryTypeList());
        $fulfilmentPolicy->setShippingOptions($this->getShippingOptions());

        if (false == empty($this->data['shipping_id'])) {
            $fulfilmentPolicy->setFulfillmentPolicyId((string) $this->data['shipping_id']);
        }

        if (false == empty($this->data['shipping_name'])) {
            $fulfilmentPolicy->setName($this->data['shipping_name']);
        }

        if (false == empty($this->data['description'])) {
            $fulfilmentPolicy->setDescription($this->data['description']);
        }

        if ($this->data['ebay_site_id'] == 0 || false == empty($this->data['ebay_site_id'])) {
            $fulfilmentPolicy->setMarketplaceId(
                $this->getMarketplaceId($this->data['ebay_site_id'])
            );
        }

        if (false == empty($this->data['dispatch_time_max'])) {
            $handlingTime = new HandlingTime();
            $handlingTime->setValue($this->data['dispatch_time_max']);
            $handlingTime->setUnit(HandlingTime::DAY);
            $fulfilmentPolicy->setHandlingTime($handlingTime);
        }

        return $fulfilmentPolicy;
    }

    public function setData($data)
    {
        $this->data = $data;
    }

    /**
     * @param int $idSite
     *
     * @return string
     */
    protected function getMarketplaceId($idSite)
    {
        return $this->getMarketplaceService()->getBySiteid($idSite);
    }

    /** @return Marketplace*/
    protected function getMarketplaceService()
    {
        return new Marketplace();
    }

    /** @return CategoryTypeList*/
    protected function getCategoryTypeList()
    {
        $categoryTypeList = new CategoryTypeList();
        $categoryType = new CategoryType();
        $categoryType->setName(CategoryType::ALL_EXCLUDING_MOTORS_VEHICLES);
        $categoryTypeList->addCategoryType($categoryType);

        return $categoryTypeList;
    }

    protected function getShippingOptions()
    {
        return $this->getShippingOptionBuilder()->build();
    }

    protected function getShippingOptionBuilder()
    {
        return new ShippingOptionBuilder($this->data);
    }
}
