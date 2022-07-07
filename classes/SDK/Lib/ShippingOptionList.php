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

class ShippingOptionList extends ResourceModel
{
    /** @var ShippingOption[] */
    protected $shippingOptions = [];

    /**
     * @return ShippingOption[]
     */
    public function getShippingOptions()
    {
        return $this->shippingOptions;
    }

    /**
     * @param ShippingOption $shippingOption
     *
     * @return self
     */
    public function addShippingOption(ShippingOption $shippingOption)
    {
        $this->shippingOptions[] = $shippingOption;

        return $this;
    }

    /**
     * @param ShippingOption[] $shippingOptions
     *
     * @return self
     */
    public function setShippingOptions($shippingOptions)
    {
        $this->shippingOptions = [];

        if (false == is_array($shippingOptions)) {
            return $this;
        }

        if (empty($shippingOptions)) {
            return $this;
        }

        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption instanceof ShippingOption) {
                $this->addShippingOption($shippingOption);
            }
        }

        return $this;
    }

    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $shippingOption) {
            $this->addShippingOption(
                (new ShippingOption())->fromArray($shippingOption)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getShippingOptions())) {
            return $return;
        }

        foreach ($this->getShippingOptions() as $shippingOption) {
            $return[] = $shippingOption->toArray();
        }

        return $return;
    }
}
