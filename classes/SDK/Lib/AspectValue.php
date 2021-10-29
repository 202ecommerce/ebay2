<?php


namespace Ebay\classes\SDK\Lib;


use Ebay\classes\SDK\Core\ResourceModel;

class AspectValue extends ResourceModel
{
    /** @var string */
    protected $localizedValue;

    /**
     * @return string
     */
    public function getLocalizedValue()
    {
        return (string)$this->localizedValue;
    }

    /**
     * @param string $localizedValue
     * @return AspectValue
     */
    public function setLocalizedValue($localizedValue)
    {
        if (false == is_string($localizedValue)) {
            return $this;
        }

        $this->localizedValue = $localizedValue;
        return $this;
    }

    public function fromArray($data)
    {
        if (false == empty($data['localizedValue'])) {
            $this->setLocalizedValue($data['localizedValue']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'localizedValue' => $this->getLocalizedValue()
        ];
    }
}