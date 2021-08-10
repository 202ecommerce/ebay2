<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class FulfilmentPolicy extends ResourceModel
{
    protected $name;

    protected $description;

    protected $marketplaceId;

    protected $categoryTypes = [];

    public function fromArray($data)
    {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['description'])) {
            $this->setDescription($data['description']);
        }

        if (isset($data['marketplaceId'])) {
            $this->setMarketplaceId($data['marketplaceId']);
        }

        if (isset($data['categoryTypes']) && false == empty($data['categoryTypes'])) {
            foreach ($data['categoryTypes'] as $categoryType) {
                $this->categoryTypes[] = (new CategoryType())->fromArray();
            }
        }

        return $this;
    }

    public function toArray()
    {
        // TODO: Implement toArray() method.
    }

    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    public function getName()
    {
        return $this->name;
    }

    public function setDescription($description)
    {
        $this->description = (string)$description;
        return $this;
    }

    public function getDescription()
    {
        return $this->description;
    }

    public function setMarketplaceId($marketplaceId)
    {
        $this->marketplaceId = (string) $marketplaceId;
        return $this;
    }

    public function getMarketplaceId()
    {
        return $this->marketplaceId;
    }
}