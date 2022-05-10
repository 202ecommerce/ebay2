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

namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShippingService extends ResourceModel
{
    /** @var int */
    protected $sortOrder;

    /** @var string */
    protected $shippingCarrierCode;

    /** @var string */
    protected $shippingServiceCode;

    /** @var ShippingCost */
    protected $shippingCost;

    /** @var AdditionalShippingCost */
    protected $additionalShippingCost;

    /** @var bool */
    protected $freeShipping;

    /** @var bool */
    protected $buyerResponsibleForShipping;

    /** @var bool */
    protected $buyerResponsibleForPickup;

    /** @var ShipToLocations */
    protected $shipToLocations;

    /**
     * @return int|null
     */
    public function getSortOrder()
    {
        return $this->sortOrder;
    }

    /**
     * @param int $sortOrder
     *
     * @return ShippingService
     */
    public function setSortOrder($sortOrder)
    {
        $this->sortOrder = (int) $sortOrder;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShippingCarrierCode()
    {
        return $this->shippingCarrierCode;
    }

    /**
     * @param string $shippingCarrierCode
     *
     * @return ShippingService
     */
    public function setShippingCarrierCode($shippingCarrierCode)
    {
        $this->shippingCarrierCode = (string) $shippingCarrierCode;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getShippingServiceCode()
    {
        return $this->shippingServiceCode;
    }

    /**
     * @param string $shippingServiceCode
     *
     * @return ShippingService
     */
    public function setShippingServiceCode($shippingServiceCode)
    {
        $this->shippingServiceCode = (string) $shippingServiceCode;

        return $this;
    }

    /**
     * @return ShippingCost|null
     */
    public function getShippingCost()
    {
        return $this->shippingCost;
    }

    /**
     * @param ShippingCost $shippingCost
     *
     * @return ShippingService
     */
    public function setShippingCost(ShippingCost $shippingCost)
    {
        $this->shippingCost = $shippingCost;

        return $this;
    }

    /**
     * @return AdditionalShippingCost|null
     */
    public function getAdditionalShippingCost()
    {
        return $this->additionalShippingCost;
    }

    /**
     * @param AdditionalShippingCost $additionalShippingCost
     *
     * @return ShippingService
     */
    public function setAdditionalShippingCost(AdditionalShippingCost $additionalShippingCost)
    {
        $this->additionalShippingCost = $additionalShippingCost;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isFreeShipping()
    {
        return $this->freeShipping;
    }

    /**
     * @param bool $freeShipping
     *
     * @return ShippingService
     */
    public function setFreeShipping($freeShipping)
    {
        $this->freeShipping = (bool) $freeShipping;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isBuyerResponsibleForShipping()
    {
        return $this->buyerResponsibleForShipping;
    }

    /**
     * @param bool $buyerResponsibleForShipping
     *
     * @return ShippingService
     */
    public function setBuyerResponsibleForShipping($buyerResponsibleForShipping)
    {
        $this->buyerResponsibleForShipping = (bool) $buyerResponsibleForShipping;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isBuyerResponsibleForPickup()
    {
        return $this->buyerResponsibleForPickup;
    }

    /**
     * @param bool $buyerResponsibleForPickup
     *
     * @return ShippingService
     */
    public function setBuyerResponsibleForPickup($buyerResponsibleForPickup)
    {
        $this->buyerResponsibleForPickup = (bool) $buyerResponsibleForPickup;

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

        if (false == empty($data['shipToLocations'])) {
            $this->setShipToLocations(
                (new ShipToLocations())->fromArray($data['shipToLocations'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (is_int($this->getSortOrder())) {
            $return['sortOrder'] = $this->getSortOrder();
        }

        if (is_string($this->getShippingCarrierCode())) {
            $return['shippingCarrierCode'] = $this->getShippingCarrierCode();
        }

        if (is_string($this->getShippingServiceCode())) {
            $return['shippingServiceCode'] = $this->getShippingServiceCode();
        }

        if ($this->getShippingCost() instanceof ShippingCost) {
            $return['shippingCost'] = $this->getShippingCost()->toArray();
        }

        if ($this->getAdditionalShippingCost() instanceof AdditionalShippingCost) {
            $return['additionalShippingCost'] = $this->getAdditionalShippingCost()->toArray();
        }

        if (is_bool($this->isFreeShipping())) {
            $return['freeShipping'] = $this->isFreeShipping();
        }

        if (is_bool($this->isBuyerResponsibleForShipping())) {
            $return['buyerResponsibleForShipping'] = $this->isBuyerResponsibleForShipping();
        }

        if (is_bool($this->isBuyerResponsibleForPickup())) {
            $return['buyerResponsibleForPickup'] = $this->isBuyerResponsibleForPickup();
        }

        if ($this->getShipToLocations() instanceof ShipToLocations) {
            $return['shipToLocations'] = $this->getShipToLocations()->toArray();
        }

        return $return;
    }

    /**
     * @param ShipToLocations $shipToLocations
     *
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
