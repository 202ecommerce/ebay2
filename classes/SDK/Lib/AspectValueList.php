<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class AspectValueList extends ResourceModel
{
    /** @var AspectValue[]*/
    protected $aspectValueList = [];

    /**
     * @param AspectValue $aspectValue
     * @return self
     */
    public function add(AspectValue $aspectValue)
    {
        $this->aspectValueList[] = $aspectValue;
        return $this;
    }

    /**
     * @param AspectValue[] $aspectValues
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
