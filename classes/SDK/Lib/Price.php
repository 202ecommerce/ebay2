<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class Price extends ResourceModel
{
    /** @var string*/
    protected $value;

    /** @var string*/
    protected $currency;

    /**
     * @return string
     */
    public function getValue()
    {
        return (string)$this->value;
    }

    /**
     * @param string $value
     * @return self
     */
    public function setValue($value)
    {
        $this->value = (string)$value;
        return $this;
    }

    /**
     * @return string
     */
    public function getCurrency()
    {
        return (string)$this->currency;
    }

    /**
     * @param string $currency
     * @return self
     */
    public function setCurrency($currency)
    {
        $this->currency = (string)$currency;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['value'])) {
            $this->setValue($data['value']);
        }

        if (isset($data['currency'])) {
            $this->setCurrency($data['currency']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'value' => $this->getValue(),
            'currency' => $this->getCurrency()
        ];
    }
}
