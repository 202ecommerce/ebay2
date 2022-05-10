<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class ShippingServiceList extends ResourceModel
{
    /** @var ShippingService[] */
    protected $shippingServices = [];

    /**
     * @param ShippingService[] $shippingServices
     *
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
     *
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
