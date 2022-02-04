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

class CategoryTypeList extends ResourceModel
{
    /** @var CategoryType[]*/
    protected $categoryTypes = [];

    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $categoryType) {
            $this->addCategoryType(
                (new CategoryType())->fromArray($categoryType)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getCategoryTypes())) {
            return $return;
        }

        foreach ($this->getCategoryTypes() as $categoryType) {
            $return[] = $categoryType->toArray();
        }

        return $return;
    }

    public function addCategoryType(CategoryType $categoryType)
    {
        $this->categoryTypes[] = $categoryType;
        return $this;
    }

    public function setCategoryTypes($categoryTypes)
    {
        $this->categoryTypes = [];

        if (false == is_array($categoryTypes)) {
            return $this;
        }

        if (empty($categoryTypes)) {
            return $this;
        }

        foreach ($categoryTypes as $categoryType) {
            if ($categoryType instanceof CategoryType) {
                $this->addCategoryType($categoryType);
            }
        }

        return $this;
    }

    public function getCategoryTypes()
    {
        return $this->categoryTypes;
    }
}
