<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

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

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'description' => $this->getDescription(),
            'marketplaceId' => $this->getMarketplaceId(),
            'categoryTypes' => $this->getCategoryTypes()->toArray(),
            'handlingTime' => $this->getHandlingTime()->toArray(),
            'shippingOptions' => $this->getShippingOptions()->toArray(),
            'globalShipping' => $this->isGlobalShipping(),
            'pickupDropOff' => $this->isPickupDropOff(),
            'freightShipping' => $this->isFreightShipping(),
            'fulfillmentPolicyId' => $this->getFulfillmentPolicyId()
        ];
    }

    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setMarketplaceId($marketplaceId)
    {
        $this->marketplaceId = (string) $marketplaceId;
        return $this;
    }

    public function getMarketplaceId()
    {
        return $this->marketplaceId;
    }

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

    public function getCategoryTypes()
    {
        return $this->categoryTypes;
    }

    public function getHandlingTime()
    {
        if ($this->handlingTime instanceof HandlingTime) {
            return $this->handlingTime;
        }

        return new HandlingTime();
    }

    /**
     * @return bool
     */
    public function isGlobalShipping()
    {
        return (bool)$this->globalShipping;
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
     * @return bool
     */
    public function isPickupDropOff()
    {
        return (bool)$this->pickupDropOff;
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
     * @return bool
     */
    public function isFreightShipping()
    {
        return (bool)$this->freightShipping;
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
     * @return string
     */
    public function getFulfillmentPolicyId()
    {
        return (string)$this->fulfillmentPolicyId;
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
     * @return ShippingOptionList
     */
    public function getShippingOptions()
    {
        if ($this->shoppingOptions instanceof ShippingOptionList) {
            return $this->shoppingOptions;
        }

        return new ShippingOptionList();
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
}