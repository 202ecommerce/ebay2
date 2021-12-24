<?php
namespace Ebay\services\Builder;

use Ebay\classes\SDK\Lib\CategoryType;
use Ebay\classes\SDK\Lib\CategoryTypeList;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\HandlingTime;
use Ebay\services\Marketplace;
use Ebay\services\MarketplaceByProfile;
use Symfony\Component\VarDumper\VarDumper;

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

        if (false == empty($this->data['shipping_name'])) {
            $fulfilmentPolicy->setFulfillmentPolicyId((string)$this->data['shipping_name']);
        }

        if (false == empty($this->data['shipping_name'])) {
            $fulfilmentPolicy->setName($this->data['shipping_name']);
        }

        if (false == empty($this->data['description'])) {
            $fulfilmentPolicy->setDescription($this->data['description']);
        }

        if (false == empty($this->data['ebay_site_id'])) {
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
