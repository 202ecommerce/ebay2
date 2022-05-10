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

class RegionList extends ResourceModel
{
    protected $regions = [];

    /**
     * @param Region $region
     *
     * @return self
     */
    public function addRegion(Region $region)
    {
        $this->regions[] = $region;

        return $this;
    }

    /**
     * @param Region[] $regions
     *
     * @return self
     */
    public function setRegions($regions)
    {
        $this->regions = [];

        if (false == is_array($regions)) {
            return $this;
        }

        if (empty($regions)) {
            return $this;
        }

        foreach ($regions as $region) {
            if ($region instanceof Region) {
                $this->addRegion($region);
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

        foreach ($data as $region) {
            $this->addRegion(
                (new Region())->fromArray($region)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getRegions())) {
            return [];
        }

        foreach ($this->getRegions() as $region) {
            $return[] = $region->toArray();
        }

        return $return;
    }

    public function getRegions()
    {
        return $this->regions;
    }
}
