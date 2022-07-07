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

class FulfilmentPolicyList extends ResourceModel
{
    /** @var FulfilmentPolicy[] */
    protected $fulfilmentPolicies = [];

    /**
     * @return self
     */
    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $fulfilmentPolicy) {
            $this->addFulfilmentPolicy(
                (new FulfilmentPolicy())->fromArray($fulfilmentPolicy)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getFulfilmentPolicies())) {
            return $return;
        }

        foreach ($this->getFulfilmentPolicies() as $fulfilmentPolicy) {
            $return[] = $fulfilmentPolicy->toArray();
        }

        return $return;
    }

    /**
     * @param FulfilmentPolicy
     *
     * @return self
     */
    public function addFulfilmentPolicy(FulfilmentPolicy $fulfilmentPolicy)
    {
        $this->fulfilmentPolicies[] = $fulfilmentPolicy;

        return $this;
    }

    public function setFulfilmentPolicies($fulfilmentPolicier)
    {
        $this->fulfilmentPolicies = [];

        if (false == is_array($fulfilmentPolicier)) {
            return $this;
        }

        if (empty($fulfilmentPolicier)) {
            return $this;
        }

        foreach ($fulfilmentPolicier as $fulfilmentPolicy) {
            if ($fulfilmentPolicy instanceof FulfilmentPolicy) {
                $this->addFulfilmentPolicy($fulfilmentPolicy);
            }
        }

        return $this;
    }

    /**
     * @return FulfilmentPolicy[]
     */
    public function getFulfilmentPolicies()
    {
        return $this->fulfilmentPolicies;
    }
}
