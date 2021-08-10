<?php


namespace Ebay\classes\SDK\Core;


class EbayApiResponse
{
    /** @var bool*/
    protected $success;

    /** @var mixed*/
    protected $result;

    /** @return bool*/
    public function isSuccess()
    {
        return $this->success;
    }

    /** @return mixed*/
    public function getResult()
    {
        return $this->result;
    }

    public function setSuccess($success)
    {
        $this->success = (bool)$success;
        return $this;
    }

    public function setResult($result)
    {
        $this->result = $result;
        return $this;
    }
}