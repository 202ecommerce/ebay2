<?php


namespace Ebay\classes\SDK\Lib;


use Ebay\classes\SDK\Core\ResourceModel;

class Region extends ResourceModel
{
    /** @var string*/
    protected $regionName;

    /**
     * @return string
     */
    public function getRegionName()
    {
        return (string)$this->regionName;
    }

    /**
     * @param string $regionName
     * @return Region
     */
    public function setRegionName($regionName)
    {
        $this->regionName = (string)$regionName;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['regionName'])) {
            $this->setRegionName($data['regionName']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'regionName' => $this->getRegionName()
        ];
    }
}