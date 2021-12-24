<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShippingServiceList extends ResourceModel
{
    /** @var ShippingService[]*/
    protected $shippingServices = [];

    /**
     * @param ShippingService[] $shippingServices
     * @return self
     */
    public function setShippingServices($shippingServices)
    {
        $this->shippingServices = [];

        if (false == is_array($shippingServices)) {
            return $this;
        }

        if (empty($shippingServices)) {
            return $this;
        }

        foreach ($shippingServices as $shippingService) {
            if ($shippingService instanceof ShippingService) {
                $this->addShippingService($shippingService);
            }
        }

        return $this;
    }

    /**
     * @param ShippingService $shippingService
     * @return self
     */
    public function addShippingService(ShippingService $shippingService)
    {
        $this->shippingServices[] = $shippingService;
        return $this;
    }

    public function fromArray($data)
    {
        if (empty($data)) {
            return $this;
        }

        foreach ($data as $shippingService) {
            $this->addShippingService(
                (new ShippingService())->fromArray($shippingService)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $return = [];

        if (empty($this->getShippingServices())) {
            return $return;
        }

        foreach ($this->getShippingServices() as $shippingService) {
            $return[] = $shippingService->toArray();
        }

        return $return;
    }

    /**
     * @return ShippingService[]
     */
    public function getShippingServices()
    {
        return $this->shippingServices;
    }
}
