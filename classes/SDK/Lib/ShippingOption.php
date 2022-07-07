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

class ShippingOption extends ResourceModel
{
    const DOMESTIC = 'DOMESTIC';

    const INTERNATIONAL = 'INTERNATIONAL';

    const FLAT_RATE = 'FLAT_RATE';

    /** @var string */
    protected $optionType;

    /** @var string */
    protected $costType;

    /** @var ShippingServiceList */
    protected $shippingServices;

    /** @var bool */
    protected $insuranceOffered;

    /** @var InsuranceFee */
    protected $insuranceFee;

    /**
     * @return string
     */
    public function getOptionType()
    {
        return (string) $this->optionType;
    }

    /**
     * @param string $optionType
     *
     * @return ShippingOption
     */
    public function setOptionType($optionType)
    {
        $this->optionType = (string) $optionType;

        return $this;
    }

    /**
     * @return string|null
     */
    public function getCostType()
    {
        return $this->costType;
    }

    /**
     * @param string $costType
     *
     * @return ShippingOption
     */
    public function setCostType($costType)
    {
        $this->costType = (string) $costType;

        return $this;
    }

    /**
     * @return ShippingServiceList|null
     */
    public function getShippingServices()
    {
        return $this->shippingServices;
    }

    /**
     * @param ShippingServiceList
     *
     * @return self
     */
    public function setShippingServices(ShippingServiceList $shippingServiceList)
    {
        $this->shippingServices = $shippingServiceList;

        return $this;
    }

    /**
     * @return bool|null
     */
    public function isInsuranceOffered()
    {
        return $this->insuranceOffered;
    }

    /**
     * @param bool $insuranceOffered
     *
     * @return ShippingOption
     */
    public function setInsuranceOffered($insuranceOffered)
    {
        $this->insuranceOffered = (bool) $insuranceOffered;

        return $this;
    }

    /**
     * @return InsuranceFee|null
     */
    public function getInsuranceFee()
    {
        return $this->insuranceFee;
    }

    /**
     * @param InsuranceFee $insuranceFee
     *
     * @return ShippingOption
     */
    public function setInsuranceFee(InsuranceFee $insuranceFee)
    {
        $this->insuranceFee = $insuranceFee;

        return $this;
    }

    /**
     * @param ShippingService
     *
     * @return self
     */
    public function addShippingService(ShippingService $shippingService)
    {
        if ($this->shippingServices instanceof ShippingServiceList == false) {
            $this->shippingServices = new ShippingServiceList();
        }

        $this->shippingServices->addShippingService($shippingService);

        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['optionType'])) {
            $this->setOptionType($data['optionType']);
        }

        if (isset($data['costType'])) {
            $this->setCostType($data['costType']);
        }

        if (isset($data['shippingServices']) && false == empty($data['shippingServices'])) {
            $this->setShippingServices(
                (new ShippingServiceList())->fromArray($data['shippingServices'])
            );
        }

        if (isset($data['insuranceOffered'])) {
            $this->setInsuranceOffered($data['insuranceOffered']);
        }

        if (isset($data['insuranceFee']) && false == empty($data['insuranceFee'])) {
            $this->setInsuranceFee(
                (new InsuranceFee())->fromArray($data['insuranceFee'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (is_string($this->getOptionType())) {
            $return['optionType'] = $this->getOptionType();
        }

        if (is_string($this->getCostType())) {
            $return['costType'] = $this->getCostType();
        }

        if (is_bool($this->isInsuranceOffered())) {
            $return['insuranceOffered'] = $this->isInsuranceOffered();
        }

        if ($this->getInsuranceFee() instanceof InsuranceFee) {
            $return['insuranceFee'] = $this->getInsuranceFee();
        }

        if ($this->getShippingServices() instanceof ShippingServiceList) {
            $return['shippingServices'] = $this->getShippingServices()->toArray();
        }

        return $return;
    }
}
