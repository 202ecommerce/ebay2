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

class HandlingTime extends ResourceModel
{
    const DAY = 'DAY';

    /** @var string*/
    protected $value;

    /** @var string*/
    protected $unit;

    /**
     * @return string
     */
    public function getValue()
    {
        return (string)$this->value;
    }

    /**
     * @param string $value
     * @return HandlingTime
     */
    public function setValue($value)
    {
        $this->value = (string)$value;
        return $this;
    }

    /**
     * @return string
     */
    public function getUnit()
    {
        return (string)$this->unit;
    }

    /**
     * @param string $unit
     * @return HandlingTime
     */
    public function setUnit($unit)
    {
        $this->unit = (string)$unit;
        return $this;
    }

    /** @return self*/
    public function fromArray($data)
    {
        if (isset($data['value'])) {
            $this->setValue($data['value']);
        }

        if (isset($data['unit'])) {
            $this->setUnit($data['unit']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'unit' => $this->getUnit()
        ];
    }
}
