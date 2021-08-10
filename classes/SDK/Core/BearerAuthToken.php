<?php


namespace Ebay\classes\SDK\Core;

use Configuration;

class BearerAuthToken
{
    /** @var \EbayProfile*/
    protected $ebayProfile;

    public function __construct(\EbayProfile $ebayProfile)
    {
        $this->ebayProfile = $ebayProfile;
    }

    public function get()
    {
        return $this->ebayProfile->getConfiguration(\ProfileConf::USER_AUTH_TOKEN);
    }
}