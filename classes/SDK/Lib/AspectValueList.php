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

class AspectValueList extends ResourceModel
{
    /** @var AspectValue[] */
    protected $aspectValueList = [];

    /**
     * @param AspectValue $aspectValue
     *
     * @return self
     */
    public function add(AspectValue $aspectValue)
    {
        $this->aspectValueList[] = $aspectValue;

        return $this;
    }

    /**
     * @param AspectValue[] $aspectValues
     *
     * @return self
     */
    public function set($aspectValues)
    {
        $this->aspectValueList = [];

        if (empty($aspectValues)) {
            return $this;
        }

        foreach ($aspectValues as $aspectValue) {
            if ($aspectValue instanceof AspectValue) {
                $this->add($aspectValue);
            }
        }

        return $this;
    }

    /**
     * @return AspectValue[]
     */
    public function getList()
    {
        return $this->aspectValueList;
    }

    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $row) {
            $this->add(
                (new AspectValue())->fromArray($row)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $output = [];

        if (empty($this->getList())) {
            return $output;
        }

        foreach ($this->getList() as $aspectValue) {
            $output[] = $aspectValue->toArray();
        }

        return $output;
    }
}
