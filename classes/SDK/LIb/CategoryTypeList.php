<?php


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