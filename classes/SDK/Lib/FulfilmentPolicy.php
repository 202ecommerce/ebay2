<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;
use Symfony\Component\VarDumper\VarDumper;

class FulfilmentPolicy extends ResourceModel
{
    /** @var string*/
    protected $name;

    /** @var string*/
    protected $description;

    /** @var string*/
    protected $marketplaceId;

    /** @var CategoryTypeList*/
    protected $categoryTypes;

    /** @var HandlingTime*/
    protected $handlingTime;

    /** @var ShipToLocations*/
    protected $shipToLocations;

    /** @var ShippingOptionList*/
    protected $shoppingOptions;

    /** @var bool*/
    protected $globalShipping;

    /** @var bool*/
    protected $pickupDropOff;

    /** @var bool*/
    protected $freightShipping;

    /** @var string*/
    protected $fulfillmentPolicyId;

    /**
     * @param mixed
     * @return self
     */
    public function fromArray($data)
    {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (isset($data['marketplaceId'])) {
            $this->setMarketplaceId($data['marketplaceId']);
        }

        if (isset($data['categoryTypes']) && false == empty($data['categoryTypes'])) {
            $this->setCategoryTypes(
                (new CategoryTypeList())->fromArray($data['categoryTypes'])
            );
        }

        if (isset($data['handlingTime']) && false == empty($data['handlingTime'])) {
            $this->setHandlingTime(
                (new HandlingTime())->fromArray($data['handlingTime'])
            );
        }

        if (isset($data['shipToLocations']) && false == empty($data['shipToLocations'])) {
            $this->setShipToLocations(
                (new ShipToLocations())->fromArray($data['shipToLocations'])
            );
        }

        if (isset($data['shippingOptions']) && false == empty($data['shippingOptions'])) {
            $this->setShippingOptions(
                (new ShippingOptionList())->fromArray($data['shippingOptions'])
            );
        }

        if (isset($data['globalShipping'])) {
            $this->setGlobalShipping($data['globalShipping']);
        }

        if (isset($data['pickupDropOff'])) {
            $this->setPickupDropOff($data['pickupDropOff']);
        }

        if (isset($data['freightShipping'])) {
            $this->setFreightShipping($data['freightShipping']);
        }

        if (isset($data['fulfillmentPolicyId'])) {
            $this->setFulfillmentPolicyId($data['fulfillmentPolicyId']);
        }

        return $this;
    }

    /** @return array*/
    public function toArray()
    {
        $return = [];

        if (is_string($this->getName())) {
            $return['name'] = $this->getName();
        }

        if (is_string($this->getDescription())) {
            $return['description'] = $this->getDescription();
        }

        if (is_string($this->getMarketplaceId())) {
            $return['marketplaceId'] = $this->getMarketplaceId();
        }

        if ($this->getCategoryTypes() instanceof CategoryTypeList) {
            $return['categoryTypes'] = $this->getCategoryTypes()->toArray();
        }

        if ($this->getHandlingTime() instanceof HandlingTime) {
            $return['handlingTime'] = $this->getHandlingTime()->toArray();
        }

        if ($this->getShipToLocations() instanceof ShipToLocations) {
            $return['shipToLocations'] = $this->getShipToLocations()->toArray();
        }

        if ($this->getShippingOptions() instanceof ShippingOptionList) {
            $return['shippingOptions'] = $this->getShippingOptions()->toArray();
        }

        if (is_bool($this->isGlobalShipping())) {
            $return['globalShipping'] = $this->isGlobalShipping();
        }

        if (is_bool($this->isPickupDropOff())) {
            $return['pickupDropOff'] = $this->isPickupDropOff();
        }

        if (is_bool($this->isFreightShipping())) {
            $return['freightShipping'] = $this->isFreightShipping();
        }

        if (is_string($this->getFulfillmentPolicyId())) {
            $return['fulfillmentPolicyId'] = $this->getFulfillmentPolicyId();
        }

        return $return;
    }

    /**
     * @param string
     * @return self
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param string
     * @return self
     */
    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    /** @return string|null*/
    public function getDescription()
    {
        return $this->description;
    }

    /**
     * @param string
     * @return self
     */
    public function setMarketplaceId($marketplaceId)
    {
        $this->marketplaceId = (string) $marketplaceId;
        return $this;
    }

    /** @return string|null*/
    public function getMarketplaceId()
    {
        return $this->marketplaceId;
    }

    /**
     * @param HandlingTime
     * @return self
     */
    public function setHandlingTime(HandlingTime $handlingTime)
    {
        $this->handlingTime = $handlingTime;
        return $this;
    }

    public function addCategoryType(CategoryType $categoryType)
    {
        if ($this->categoryTypes instanceof CategoryTypeList == false) {
            $this->categoryTypes = new CategoryTypeList();
        }

        $this->categoryTypes->addCategoryType($categoryType);
        return $this;
    }

    /**
     * @param CategoryTypeList $categoryTypeList
     * @return self
     */
    public function setCategoryTypes(CategoryTypeList $categoryTypeList)
    {
        $this->categoryTypes = $categoryTypeList;
        return $this;
    }

    /**
     * @return  CategoryTypeList|null
     */
    public function getCategoryTypes()
    {
        return $this->categoryTypes;
    }

    /**
     * @return  HandlingTime|null
     */
    public function getHandlingTime()
    {
        return $this->handlingTime;
    }

    /**
     * @return bool|null
     */
    public function isGlobalShipping()
    {
        return $this->globalShipping;
    }

    /**
     * @param bool $globalShipping
     * @return self
     */
    public function setGlobalShipping($globalShipping)
    {
        $this->globalShipping = (bool)$globalShipping;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isPickupDropOff()
    {
        return $this->pickupDropOff;
    }

    /**
     * @param bool $pickupDropOff
     * @return self
     */
    public function setPickupDropOff($pickupDropOff)
    {
        $this->pickupDropOff = (bool)$pickupDropOff;
        return $this;
    }

    /**
     * @return bool|null
     */
    public function isFreightShipping()
    {
        return $this->freightShipping;
    }

    /**
     * @param bool $freightShipping
     * @return self
     */
    public function setFreightShipping($freightShipping)
    {
        $this->freightShipping = (bool)$freightShipping;
        return $this;
    }

    /**
     * @return string|null
     */
    public function getFulfillmentPolicyId()
    {
        return $this->fulfillmentPolicyId;
    }

    /**
     * @param string $fulfillmentPolicyId
     * @return self
     */
    public function setFulfillmentPolicyId($fulfillmentPolicyId)
    {
        $this->fulfillmentPolicyId = (string)$fulfillmentPolicyId;
        return $this;
    }

    /**
     * @param ShippingOptionList $shippingOptionList
     * @return self
     */
    public function setShippingOptions(ShippingOptionList $shippingOptionList)
    {
        $this->shoppingOptions = $shippingOptionList;
        return $this;
    }

    /**
     * @return ShippingOptionList|null
     */
    public function getShippingOptions()
    {
        return $this->shoppingOptions;
    }

    /**
     * @param ShippingOption $shippingOption
     * @return self
     */
    public function addShippingOption(ShippingOption $shippingOption)
    {
        if ($this->shoppingOptions instanceof ShippingOptionList == false) {
            $this->shoppingOptions = new ShippingOptionList();
        }

        $this->shoppingOptions->addShippingOption($shippingOption);
        return $this;
    }

    /**
     * @param ShipToLocations $shipToLocations
     * @return self
     */
    public function setShipToLocations(ShipToLocations $shipToLocations)
    {
        $this->shipToLocations = $shipToLocations;
        return $this;
    }

    /** @return ShipToLocations|null */
    public function getShipToLocations()
    {
        return $this->shipToLocations;
    }
}
