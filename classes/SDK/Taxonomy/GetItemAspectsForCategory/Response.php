<?php
namespace Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory;

use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Lib\AspectList;

class Response extends EbayApiResponse
{
    /** @var AspectList */
    protected $aspectList;

    public function setAspectList(AspectList $aspectList)
    {
        $this->aspectList = $aspectList;
        return $this;
    }

    public function getAspectList()
    {
        if ($this->aspectList instanceof AspectList) {
            return $this->aspectList;
        }

        return new AspectList();
    }
}
