<?php
namespace Ebay\services;

class MarketplaceByProfile
{
    /**
     * @param \EbayProfile
     * @return string
     */
    public function get(\EbayProfile $ebayProfile)
    {
        foreach (\EbaySiteMap::get() as $map) {
            if ($map['site_id'] == $ebayProfile->ebay_site_id) {
                return strtoupper('ebay_' . $map['site_extension']);
            }
        }

        return '';
    }
}