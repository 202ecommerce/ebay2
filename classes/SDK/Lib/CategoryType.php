<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;

class CategoryType extends ResourceModel
{
    const ALL_EXCLUDING_MOTORS_VEHICLES = 'ALL_EXCLUDING_MOTORS_VEHICLES';

    protected $name;

    protected $default;

    /**
     * @return mixed
     */
    public function getName()
    {
        return $this->name;
    }

    /**
     * @param mixed $name
     * @return CategoryType
     */
    public function setName($name)
    {
        $this->name = (string)$name;
        return $this;
    }

    /**
     * @return mixed
     */
    public function getDefault()
    {
        return $this->default;
    }

    /**
     * @param mixed $default
     * @return CategoryType
     */
    public function setDefault($default)
    {
        $this->default = (bool)$default;
        return $this;
    }

    public function fromArray($data)
    {
        if (isset($data['name'])) {
            $this->setName($data['name']);
        }

        if (isset($data['default'])) {
            $this->setDefault($data['default']);
        }

        return $this;
    }

    public function toArray()
    {
        return [
            'name' => $this->getName(),
            'default' => $this->getDefault()
        ];
    }
}
