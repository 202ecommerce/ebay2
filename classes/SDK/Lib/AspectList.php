<?php
namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;
use Symfony\Component\VarDumper\VarDumper;

class AspectList extends ResourceModel
{
    /** @var Aspect[]*/
    protected $aspectList = [];

    /**
     * @param array $data
     * @return self
     */
    public function fromArray($data)
    {
        if (empty($data['aspects'])) {
            return $this;
        }

        if (false == is_array($data['aspects'])) {
            return $this;
        }

        foreach ($data['aspects'] as $row) {
            $this->add(
                (new Aspect())->fromArray($row)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $output = [];

        if (empty($this->getList())) {
            return $output;
        }

        foreach ($this->getList() as $aspect) {
            $output[] = $aspect->toArray();
        }

        return $output;
    }

    /**
     * @return Aspect[]
     */
    public function getList()
    {
        return $this->aspectList;
    }

    /**
     * @param Aspect $aspect
     * @return self
     */
    public function add(Aspect $aspect)
    {
        $this->aspectList[] = $aspect;
        return $this;
    }

    public function set($aspects)
    {
        $this->aspectList = [];

        foreach ($aspects as $aspect) {
            if ($aspect instanceof Aspect) {
                $this->add($aspect);
            }
        }

        return $this;
    }
}
