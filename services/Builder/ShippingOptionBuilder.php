<?php


namespace Ebay\services\Builder;


use Ebay\classes\SDK\Lib\AdditionalShippingCost;
use Ebay\classes\SDK\Lib\ShippingCost;
use Ebay\classes\SDK\Lib\ShippingOption;
use Ebay\classes\SDK\Lib\ShippingOptionList;
use Ebay\classes\SDK\Lib\ShippingService;
use Ebay\classes\SDK\Lib\ShippingServiceList;
use Symfony\Component\VarDumper\VarDumper;

class ShippingOptionBuilder implements BuilderInterface
{
    protected $data;

    public function __construct($data)
    {
        $this->setData($data);
    }

    /** @return ShippingOptionList*/
    public function build()
    {
        $shippingOptionList = new ShippingOptionList();

        if (isset($this->data['currency_id']) == false) {
            return $shippingOptionList;
        }

        if (isset($this->data['national_services']) && false == empty($this->data['national_services'])) {
            $shippingOption = $this->buildShippingOption($this->data['national_services'], ShippingOption::DOMESTIC, $this->data['currency_id']);
            $shippingOptionList->addShippingOption($shippingOption);
        }

        return $shippingOptionList;
    }

    public function setData($data)
    {
        $this->data = $data;
        return $this;
    }

    protected function buildShippingOption($shippingData, $type, $currency)
    {
        $shippingOption = new ShippingOption();
        $shippingOption->setCostType(ShippingOption::FLAT_RATE);
        $shippingOption->setOptionType($type);

        foreach ($shippingData as $serviceName => $services) {
            foreach ($services as $service) {
                $shippingService = new ShippingService();
                $shippingService->setShippingServiceCode($serviceName);

                if (false == isset($service['serviceCosts'])) {
                    continue;
                }

                if (isset($service['servicePriority'])) {
                    $shippingService->setSortOrder($service['servicePriority']);
                }

                if (isset($service['serviceAdditionalCosts'])) {
                    $additionalShippingCost = new AdditionalShippingCost();
                    $additionalShippingCost->setValue($service['serviceAdditionalCosts']);
                    $additionalShippingCost->setCurrency($currency);
                    $shippingService->setAdditionalShippingCost($additionalShippingCost);
                }

                $shippingCost = new ShippingCost();
                $shippingCost->setValue($service['serviceCosts']);
                $shippingCost->setCurrency($currency);
                $shippingService->setShippingCost($shippingCost);
                $shippingOption->addShippingService($shippingService);
            }
        }

        return $shippingOption;
    }
}