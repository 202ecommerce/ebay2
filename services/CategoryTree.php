<?php

namespace Ebay\services;

use EbayProfile;

class CategoryTree
{
    public function getIdByProfile(EbayProfile $ebayProfile)
    {
        return $ebayProfile->ebay_site_id;
    }
}