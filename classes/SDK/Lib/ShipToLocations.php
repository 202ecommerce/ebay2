<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShipToLocations extends ResourceModel
{
    /** @var RegionList*/
    protected $regionIncluded;

    /** @var RegionList*/
    protected $regionExcluded;

    /**
     * @return RegionList
     */
    public function getRegionIncluded()
    {
        if ($this->regionIncluded instanceof RegionList) {
            return $this->regionIncluded;
        }

        return new RegionList();
    }

    /**
     * @param RegionList $regionIncluded
     * @return ShipToLocations
     */
    public function setRegionIncluded(RegionList $regionIncluded)
    {
        $this->regionIncluded = $regionIncluded;
        return $this;
    }

    /**
     * @return RegionList
     */
    public function getRegionExcluded()
    {
        if ($this->regionExcluded instanceof RegionList) {
            return $this->regionExcluded;
        }

        return new RegionList();
    }

    /**
     * @param RegionList $regionExcluded
     * @return ShipToLocations
     */
    public function setRegionExcluded($regionExcluded)
    {
        $this->regionExcluded = $regionExcluded;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['regionIncluded']) && false == empty($data['regionIncluded'])) {
            $this->setRegionIncluded(
                (new RegionList())->fromArray($data['regionIncluded'])
            );
        }

        if (isset($data['regionExcluded']) && false == empty($data['regionExcluded'])) {
            $this->setRegionExcluded(
                (new RegionList())->fromArray($data['regionExcluded'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'regionIncluded' => $this->getRegionIncluded()->toArray(),
            'regionExcluded' => $this->getRegionExcluded()->toArray()
        ];
    }
}
