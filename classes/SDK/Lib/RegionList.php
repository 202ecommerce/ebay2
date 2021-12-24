<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class RegionList extends ResourceModel
{
    protected $regions = [];

    /**
     * @param Region $region
     * @return self
     */
    public function addRegion(Region $region)
    {
        $this->regions[] = $region;
        return $this;
    }

    /**
     * @param Region[] $regions
     * @return self
     */
    public function setRegions($regions)
    {
        $this->regions = [];

        if (false == is_array($regions)) {
            return $this;
        }

        if (empty($regions)) {
            return $this;
        }

        foreach ($regions as $region) {
            if ($region instanceof Region) {
                $this->addRegion($region);
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

        foreach ($data as $region) {
            $this->addRegion(
                (new Region())->fromArray($region)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getRegions())) {
            return [];
        }

        foreach ($this->getRegions() as $region) {
            $return[] = $region->toArray();
        }

        return $return;
    }

    public function getRegions()
    {
        return $this->regions;
    }
}
