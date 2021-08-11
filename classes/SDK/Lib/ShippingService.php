<?php


namespace Ebay\classes\SDK\Lib;


use Ebay\classes\SDK\Core\ResourceModel;

class ShippingService extends ResourceModel
{
    /** @var int*/
    protected $sortOrder;

    /** @var string*/
    protected $shippingCarrierCode;

    /** @var string*/
    protected $shippingServiceCode;

    /** @var ShippingCost*/
    protected $shippingCost;

    /** @var AdditionalShippingCost*/
    protected $additionalShippingCost;

    /** @var bool*/
    protected $freeShipping;

    /** @var bool*/
    protected $buyerResponsibleForShipping;

    /** @var bool*/
    protected $buyerResponsibleForPickup;

    /**
     * @return int
     */
    public function getSortOrder()
    {
        return (int)$this->sortOrder;
    }

    /**
     * @param int $sortOrder
     * @return ShippingService
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = (int)$sortOrder;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingCarrierCode()
    {
        return (string)$this->shippingCarrierCode;
    }

    /**
     * @param string $shippingCarrierCode
     * @return ShippingService
     */
    public function setShippingCarrierCode($shippingCarrierCode)
    {
        $this->shippingCarrierCode = (string)$shippingCarrierCode;
        return $this;
    }

    /**
     * @return string
     */
    public function getShippingServiceCode()
    {
        return (string)$this->shippingServiceCode;
    }

    /**
     * @param string $shippingServiceCode
     * @return ShippingService
     */
    public function setShippingServiceCode($shippingServiceCode)
    {
        $this->shippingServiceCode = (string)$shippingServiceCode;
        return $this;
    }

    /**
     * @return ShippingCost
     */
    public function getShippingCost()
    {
        if ($this->shippingCost instanceof ShippingCost) {
            return $this->shippingCost;
        }

        return new ShippingCost();
    }

    /**
     * @param ShippingCost $shippingCost
     * @return ShippingService
     */
    public function setShippingCost(ShippingCost $shippingCost)
    {
        $this->shippingCost = $shippingCost;
        return $this;
    }

    /**
     * @return AdditionalShippingCost
     */
    public function getAdditionalShippingCost()
    {
        if ($this->additionalShippingCost instanceof AdditionalShippingCost) {
            return $this->additionalShippingCost;
        }

        return new AdditionalShippingCost();
    }

    /**
     * @param AdditionalShippingCost $additionalShippingCost
     * @return ShippingService
     */
    public function setAdditionalShippingCost(AdditionalShippingCost $additionalShippingCost)
    {
        $this->additionalShippingCost = $additionalShippingCost;
        return $this;
    }

    /**
     * @return bool
     */
    public function isFreeShipping()
    {
        return (bool)$this->freeShipping;
    }

    /**
     * @param bool $freeShipping
     * @return ShippingService
     */
    public function setFreeShipping($freeShipping)
    {
        $this->freeShipping = (bool)$freeShipping;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBuyerResponsibleForShipping()
    {
        return (bool)$this->buyerResponsibleForShipping;
    }

    /**
     * @param bool $buyerResponsibleForShipping
     * @return ShippingService
     */
    public function setBuyerResponsibleForShipping($buyerResponsibleForShipping)
    {
        $this->buyerResponsibleForShipping = (bool)$buyerResponsibleForShipping;
        return $this;
    }

    /**
     * @return bool
     */
    public function isBuyerResponsibleForPickup()
    {
        return (bool)$this->buyerResponsibleForPickup;
    }

    /**
     * @param bool $buyerResponsibleForPickup
     * @return ShippingService
     */
    public function setBuyerResponsibleForPickup($buyerResponsibleForPickup)
    {
        $this->buyerResponsibleForPickup = (bool)$buyerResponsibleForPickup;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['sortOrder'])) {
            $this->setSortOrder($data['sortOrder']);
        }

        if (isset($data['shippingCarrierCode'])) {
            $this->setShippingCarrierCode($data['shippingCarrierCode']);
        }

        if (isset($data['shippingServiceCode'])) {
            $this->setShippingServiceCode($data['shippingServiceCode']);
        }

        if (isset($data['shippingCost']) && false == empty($data['shippingCost'])) {
            $this->setShippingCost(
                (new ShippingCost())->fromArray($data['shippingCost'])
            );
        }

        if (isset($data['additionalShippingCost']) && false == empty($data['additionalShippingCost'])) {
            $this->setAdditionalShippingCost(
                (new AdditionalShippingCost())->fromArray($data['additionalShippingCost'])
            );
        }

        if (isset($data['freeShipping'])) {
            $this->setFreeShipping($data['freeShipping']);
        }

        if (isset($data['buyerResponsibleForShipping'])) {
            $this->setBuyerResponsibleForShipping($data['buyerResponsibleForShipping']);
        }

        if (isset($data['buyerResponsibleForPickup'])) {
            $this->setBuyerResponsibleForPickup($data['buyerResponsibleForPickup']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'sortOrder' => $this->getSortOrder(),
            'shippingCarrierCode' => $this->getShippingCarrierCode(),
            'shippingServiceCode' => $this->getShippingServiceCode(),
            'shippingCost' => $this->getShippingCost()->toArray(),
            'additionalShippingCost' => $this->getAdditionalShippingCost()->toArray(),
            'freeShipping' => $this->isFreeShipping(),
            'buyerResponsibleForShipping' => $this->isBuyerResponsibleForShipping(),
            'buyerResponsibleForPickup' => $this->isBuyerResponsibleForPickup()
        ];
    }
}