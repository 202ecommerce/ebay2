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
 *
 */

namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShipToLocations extends ResourceModel
{
    /** @var RegionList*/
    protected $regionIncluded;

    /** @var RegionList*/
    protected $regionExcluded;

    /**
     * @return RegionList
     */
    public function getRegionIncluded()
    {
        if ($this->regionIncluded instanceof RegionList) {
            return $this->regionIncluded;
        }

        return new RegionList();
    }

    /**
     * @param RegionList $regionIncluded
     * @return ShipToLocations
     */
    public function setRegionIncluded(RegionList $regionIncluded)
    {
        $this->regionIncluded = $regionIncluded;
        return $this;
    }

    /**
     * @return RegionList
     */
    public function getRegionExcluded()
    {
        if ($this->regionExcluded instanceof RegionList) {
            return $this->regionExcluded;
        }

        return new RegionList();
    }

    /**
     * @param RegionList $regionExcluded
     * @return ShipToLocations
     */
    public function setRegionExcluded($regionExcluded)
    {
        $this->regionExcluded = $regionExcluded;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['regionIncluded']) && false == empty($data['regionIncluded'])) {
            $this->setRegionIncluded(
                (new RegionList())->fromArray($data['regionIncluded'])
            );
        }

        if (isset($data['regionExcluded']) && false == empty($data['regionExcluded'])) {
            $this->setRegionExcluded(
                (new RegionList())->fromArray($data['regionExcluded'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'regionIncluded' => $this->getRegionIncluded()->toArray(),
            'regionExcluded' => $this->getRegionExcluded()->toArray()
        ];
    }
}
