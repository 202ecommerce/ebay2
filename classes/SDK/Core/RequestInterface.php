<?php
namespace Ebay\classes\SDK\Core;

interface RequestInterface
{
    /** @return string*/
    public function getEndPoint();

    /** @return string*/
    public function toJson();

    /** @return array*/
    public function toArray();

    /** @return string*/
    public function getMethod();

    /** @return array*/
    public function getOptions();
}
