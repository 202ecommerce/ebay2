<?php


namespace Ebay\classes\SDK\Core;


abstract class ResourceModel
{
    abstract public function fromArray($data);

    public function fromJson($data)
    {
        return $this->fromArray(json_decode($data, true));
    }

    abstract public function toArray();

    public function toJson()
    {
        return json_encode($this->toArray());
    }
}