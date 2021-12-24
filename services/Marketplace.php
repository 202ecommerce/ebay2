<?php
namespace Ebay\services;

class Marketplace
{
    /**
     * @param \EbayProfile
     * @return string
     */
    public function getByProfile(\EbayProfile $ebayProfile)
    {
        return $this->getBySiteId($ebayProfile->ebay_site_id);
    }

    public function getBySiteId($idSite)
    {
        foreach (\EbaySiteMap::get() as $map) {
            if ($map['site_id'] == $idSite) {
                return \Tools::strtoupper('ebay_' . $map['site_extension']);
            }
        }

        return '';
    }
}
