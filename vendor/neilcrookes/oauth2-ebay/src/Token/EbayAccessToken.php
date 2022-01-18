<?php

namespace EbayVendor\NeilCrookes\OAuth2\Client\Token;

use EbayVendor\League\OAuth2\Client\Token\AccessToken;
class EbayAccessToken extends AccessToken
{
    /**
     * @param string $resourceOwnerId
     */
    public function setResourceOwnerId($resourceOwnerId)
    {
        $this->resourceOwnerId = $resourceOwnerId;
    }
}
