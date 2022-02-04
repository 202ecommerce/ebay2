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

class Aspect extends ResourceModel
{
    /** @var string*/
    protected $localizedAspectName;

    /** @var AspectValueList*/
    protected $aspectValues;

    /** @var AspectConstraint*/
    protected $aspectConstraint;

    /**
     * @return string
     */
    public function getLocalizedAspectName()
    {
        return $this->localizedAspectName;
    }

    /**
     * @param string $localizedAspectName
     * @return self
     */
    public function setLocalizedAspectName($localizedAspectName)
    {
        if (false == is_string($localizedAspectName)) {
            return $this;
        }

        $this->localizedAspectName = $localizedAspectName;
        return $this;
    }

    /**
     * @return AspectValueList
     */
    public function getAspectValues()
    {
        if ($this->aspectValues instanceof AspectValueList) {
            return $this->aspectValues;
        }

        return new AspectValueList();
    }

    /**
     * @param AspectValueList $aspectValues
     * @return Aspect
     */
    public function setAspectValues(AspectValueList $aspectValues)
    {
        $this->aspectValues = $aspectValues;
        return $this;
    }

    /**
     * @return AspectConstraint
     */
    public function getAspectConstraint()
    {
        if ($this->aspectConstraint instanceof AspectConstraint) {
            return $this->aspectConstraint;
        }

        return new AspectConstraint();
    }

    /**
     * @param AspectConstraint $aspectConstraint
     * @return Aspect
     */
    public function setAspectConstraint(AspectConstraint $aspectConstraint)
    {
        $this->aspectConstraint = $aspectConstraint;
        return $this;
    }

    public function fromArray($data)
    {
        if (false == empty($data['localizedAspectName'])) {
            $this->setLocalizedAspectName($data['localizedAspectName']);
        }

        if (false == empty($data['aspectConstraint'])) {
            $this->setAspectConstraint(
                (new AspectConstraint())->fromArray($data['aspectConstraint'])
            );
        }

        if (false == empty($data['aspectValues'])) {
            $this->setAspectValues(
                (new AspectValueList())->fromArray($data['aspectValues'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'localizedAspectName' => $this->getLocalizedAspectName(),
            'aspectConstraint' => $this->getAspectConstraint()->toArray(),
            'aspectValues' => $this->getAspectValues()->toArray()
        ];
    }
}
