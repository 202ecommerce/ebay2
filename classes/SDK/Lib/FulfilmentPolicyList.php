<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class FulfilmentPolicyList extends ResourceModel
{
    /** @var FulfilmentPolicy[]*/
    protected $fulfilmentPolicies = [];

    /**
     * @return self
     */
    public function fromArray($data)
    {
        if (false == is_array($data)) {
            return $this;
        }

        if (empty($data)) {
            return $this;
        }

        foreach ($data as $fulfilmentPolicy) {
            $this->addFulfilmentPolicy(
                (new FulfilmentPolicy())->fromArray($fulfilmentPolicy)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getFulfilmentPolicies())) {
            return $return;
        }

        foreach ($this->getFulfilmentPolicies() as $fulfilmentPolicy) {
            $return[] = $fulfilmentPolicy->toArray();
        }

        return $return;
    }

    /**
     * @param FulfilmentPolicy
     * @return self
     */
    public function addFulfilmentPolicy(FulfilmentPolicy $fulfilmentPolicy)
    {
        $this->fulfilmentPolicies[] = $fulfilmentPolicy;
        return $this;
    }

    public function setFulfilmentPolicies($fulfilmentPolicier)
    {
        $this->fulfilmentPolicies = [];

        if (false == is_array($fulfilmentPolicier)) {
            return $this;
        }

        if (empty($fulfilmentPolicier)) {
            return $this;
        }

        foreach ($fulfilmentPolicier as $fulfilmentPolicy) {
            if ($fulfilmentPolicy instanceof FulfilmentPolicy) {
                $this->addFulfilmentPolicy($fulfilmentPolicy);
            }
        }

        return $this;
    }

    /**
     * @return FulfilmentPolicy[]
     */
    public function getFulfilmentPolicies()
    {
        return $this->fulfilmentPolicies;
    }
}
