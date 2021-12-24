<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShippingOptionList extends ResourceModel
{
    /** @var ShippingOption[]*/
    protected $shippingOptions = [];

    /**
     * @return ShippingOption[]
     */
    public function getShippingOptions()
    {
        return $this->shippingOptions;
    }

    /**
     * @param ShippingOption $shippingOption
     * @return self
     */
    public function addShippingOption(ShippingOption $shippingOption)
    {
        $this->shippingOptions[] = $shippingOption;
        return $this;
    }

    /**
     * @param ShippingOption[] $shippingOptions
     * @return self
     */
    public function setShippingOptions($shippingOptions)
    {
        $this->shippingOptions = [];

        if (false == is_array($shippingOptions)) {
            return $this;
        }

        if (empty($shippingOptions)) {
            return $this;
        }

        foreach ($shippingOptions as $shippingOption) {
            if ($shippingOption instanceof ShippingOption) {
                $this->addShippingOption($shippingOption);
            }
        }

        return $this;
    }

    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $shippingOption) {
            $this->addShippingOption(
                (new ShippingOption())->fromArray($shippingOption)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getShippingOptions())) {
            return $return;
        }

        foreach ($this->getShippingOptions() as $shippingOption) {
            $return[] = $shippingOption->toArray();
        }

        return $return;
    }
}
