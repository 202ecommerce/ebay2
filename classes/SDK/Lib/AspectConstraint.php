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
use Symfony\Component\VarDumper\VarDumper;

class AspectConstraint extends ResourceModel
{
    const MODE_SELECT_ONLY = 'SELECTION_ONLY';

    const MODE_FREE_TEXT = 'FREE_TEXT';

    /** @var string*/
    protected $aspectDataType;

    /** @var string*/
    protected $itemToAspectCardinality;

    /** @var string*/
    protected $aspectMode;

    /** @var bool*/
    protected $aspectRequired;

    /** @var string*/
    protected $aspectUsage;

    /** @var bool*/
    protected $aspectEnabledForVariations;

    /** @var int*/
    protected $aspectMaxLength;

    /**
     * @return int|null
     */
    public function getAspectMaxLength()
    {
        return $this->aspectMaxLength;
    }

    /**
     * @param int $maxLength
     * @return self
     */
    public function setAspectMaxLength($maxLength)
    {
        $this->aspectMaxLength = (int)$maxLength;
        return $this;
    }

    /**
     * @return string
     */
    public function getAspectDataType()
    {
        return (string)$this->aspectDataType;
    }

    /**
     * @param string $aspectDataType
     * @return self
     */
    public function setAspectDataType($aspectDataType)
    {
        if (false == is_string($aspectDataType)) {
            return $this;
        }

        $this->aspectDataType = $aspectDataType;
        return $this;
    }

    /**
     * @return string
     */
    public function getItemToAspectCardinality()
    {
        return (string)$this->itemToAspectCardinality;
    }

    /**
     * @param string $itemToAspectCardinality
     * @return self
     */
    public function setItemToAspectCardinality($itemToAspectCardinality)
    {
        if (false == is_string($itemToAspectCardinality)) {
            return $this;
        }

        $this->itemToAspectCardinality = $itemToAspectCardinality;
        return $this;
    }

    /**
     * @return string
     */
    public function getAspectMode()
    {
        return (string)$this->aspectMode;
    }

    /**
     * @param string $aspectMode
     * @return self
     */
    public function setAspectMode($aspectMode)
    {
        if (false == is_string($aspectMode)) {
            return $this;
        }

        $this->aspectMode = $aspectMode;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAspectRequired()
    {
        return (bool)$this->aspectRequired;
    }

    /**
     * @param bool $aspectRequired
     * @return self
     */
    public function setAspectRequired($aspectRequired)
    {
        $this->aspectRequired = (bool)$aspectRequired;
        return $this;
    }

    /**
     * @return string
     */
    public function getAspectUsage()
    {
        return (string)$this->aspectUsage;
    }

    /**
     * @param string $aspectUsage
     * @return self
     */
    public function setAspectUsage($aspectUsage)
    {
        if (false == is_string($aspectUsage)) {
            return $this;
        }

        $this->aspectUsage = $aspectUsage;
        return $this;
    }

    /**
     * @return bool
     */
    public function isAspectEnabledForVariations()
    {
        return (bool)$this->aspectEnabledForVariations;
    }

    /**
     * @param bool $aspectEnabledForVariations
     * @return self
     */
    public function setAspectEnabledForVariations($aspectEnabledForVariations)
    {
        $this->aspectEnabledForVariations = (bool)$aspectEnabledForVariations;
        return $this;
    }

    public function fromArray($data)
    {
        if (false == empty($data['aspectDataType'])) {
            $this->setAspectDataType($data['aspectDataType']);
        }

        if (false == empty($data['itemToAspectCardinality'])) {
            $this->setItemToAspectCardinality($data['itemToAspectCardinality']);
        }

        if (false == empty($data['aspectMode'])) {
            $this->setAspectMode($data['aspectMode']);
        }

        if (isset($data['aspectRequired'])) {
            $this->setAspectRequired($data['aspectRequired']);
        }

        if (false == empty($data['aspectUsage'])) {
            $this->setAspectUsage($data['aspectUsage']);
        }

        if (isset($data['aspectEnabledForVariations'])) {
            $this->setAspectEnabledForVariations($data['aspectEnabledForVariations']);
        }

        if (false == empty($data['aspectMaxLength'])) {
            $this->setAspectMaxLength($data['aspectMaxLength']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'aspectDataType' => $this->getAspectDataType(),
            'itemToAspectCardinality' => $this->getItemToAspectCardinality(),
            'aspectMode' => $this->getAspectMode(),
            'aspectRequired' => $this->isAspectRequired(),
            'aspectUsage' => $this->getAspectUsage(),
            'aspectEnabledForVariations' => $this->isAspectEnabledForVariations()
        ];
    }
}
