<?php


namespace Ebay\classes\SDK\Lib;


use Ebay\classes\SDK\Core\ResourceModel;

class ShippingOption extends ResourceModel
{
    /** @var string*/
    protected $optionType;

    /** @var string*/
    protected $costType;

    /** @var ShippingServiceList*/
    protected $shippingServices;

    /** @var bool*/
    protected $insuranceOffered;

    /** @var InsuranceFee*/
    protected $insuranceFee;

    /**
     * @return string
     */
    public function getOptionType()
    {
        return (string)$this->optionType;
    }

    /**
     * @param string $optionType
     * @return ShippingOption
     */
    public function setOptionType($optionType)
    {
        $this->optionType = (string)$optionType;
        return $this;
    }

    /**
     * @return string
     */
    public function getCostType()
    {
        return (string)$this->costType;
    }

    /**
     * @param string $costType
     * @return ShippingOption
     */
    public function setCostType($costType)
    {
        $this->costType = (string)$costType;
        return $this;
    }

    /**
     * @return ShippingServiceList
     */
    public function getShippingServices()
    {
        if ($this->shippingServices instanceof ShippingServiceList) {
            return $this->shippingServices;
        }

        return new ShippingServiceList();
    }

    /**
     * @param ShippingServiceList
     * @return self
     */
    public function setShippingServices(ShippingServiceList $shippingServiceList)
    {
        $this->shippingServices = $shippingServiceList;
        return $this;
    }

    /**
     * @return bool
     */
    public function isInsuranceOffered()
    {
        return (bool)$this->insuranceOffered;
    }

    /**
     * @param bool $insuranceOffered
     * @return ShippingOption
     */
    public function setInsuranceOffered($insuranceOffered)
    {
        $this->insuranceOffered = (bool)$insuranceOffered;
        return $this;
    }

    /**
     * @return InsuranceFee
     */
    public function getInsuranceFee()
    {
        if ($this->insuranceFee instanceof InsuranceFee) {
            return $this->insuranceFee;
        }

        return new InsuranceFee();
    }

    /**
     * @param InsuranceFee $insuranceFee
     * @return ShippingOption
     */
    public function setInsuranceFee(InsuranceFee $insuranceFee)
    {
        $this->insuranceFee = $insuranceFee;
        return $this;
    }

    /**
     * @param ShippingService
     * @return self
     */
    public function addShippingService(ShippingService $shippingService)
    {
        if ($this->shippingServices instanceof ShippingServiceList == false) {
            $this->shippingServices = new ShippingServiceList();
        }

        $this->shippingServices->addShippingService($shippingService);
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['optionType'])) {
            $this->setOptionType($data['optionType']);
        }

        if (isset($data['costType'])) {
            $this->setCostType($data['costType']);
        }

        if (isset($data['shippingServices']) && false == empty($data['shippingServices'])) {
            $this->setShippingServices(
                (new ShippingServiceList())->fromArray($data['shippingServices'])
            );
        }

        if (isset($data['insuranceOffered'])) {
            $this->setInsuranceOffered($data['insuranceOffered']);
        }

        if (isset($data['insuranceFee']) && false == empty($data['insuranceFee'])) {
            $this->setInsuranceFee(
                (new InsuranceFee())->fromArray($data['insuranceFee'])
            );
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'optionType' => $this->getOptionType(),
            'costType' => $this->getCostType(),
            'insuranceOffered' => $this->isInsuranceOffered(),
            'insuranceFee' => $this->getInsuranceFee()->toArray(),
            'shippingServices' => $this->getShippingServices()->toArray()
        ];
    }
}