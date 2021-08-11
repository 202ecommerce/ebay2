<?php


namespace Ebay\classes\SDK\Lib;


use Ebay\classes\SDK\Core\ResourceModel;

class HandlingTime extends ResourceModel
{
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