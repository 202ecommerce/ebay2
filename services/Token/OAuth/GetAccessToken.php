<?php
/**
 *  2007-2022 PrestaShop
 *
 *  NOTICE OF LICENSE
 *
 *  This source file is subject to the Academic Free License (AFL 3.0)
 *  that is bundled with this package in the file LICENSE.txt.
 *  It is also available through the world-wide-web at this URL:
 *  http://opensource.org/licenses/afl-3.0.php
 *  If you did not receive a copy of the license and are unable to
 *  obtain it through the world-wide-web, please send an email
 *  to license@prestashop.com so we can send you a copy immediately.
 *
 *  DISCLAIMER
 *
 *  Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 *  versions in the future. If you wish to customize PrestaShop for your
 *  needs please refer to http://www.prestashop.com for more information.
 *
 *  @author 202-ecommerce <tech@202-ecommerce.com>
 *  @copyright Copyright (c) 2007-2022 202-ecommerce
 *  @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

namespace Ebay\services\Token\OAuth;

use Ebay\classes\SandboxMode;
use EbayProfile;
use Exception;
use ProfileConf;
use Throwable;

class GetAccessToken
{
    /**
     * @param EbayProfile $ebayProfile
     * @param string $code temp auth code from ebay
     *
     * @return ResponseGetAccessToken
     */
    public function get(EbayProfile $ebayProfile, $code)
    {
        $response = new ResponseGetAccessToken();

        try {
            $provider = new \EbayVendor\NeilCrookes\OAuth2\Client\Provider\Ebay([
                'clientId' => $ebayProfile->getConfiguration(ProfileConf::APP_ID),
                'clientSecret' => $ebayProfile->getConfiguration(ProfileConf::CERT_ID),
                'redirectUri' => $ebayProfile->getConfiguration(ProfileConf::RU_NAME),
                'sandbox' => (new SandboxMode())->isSandbox(),
                'http_errors' => false, // Optional. Means Guzzle Exceptions aren't thrown on 4xx or 5xx responses from eBay, allowing you to detect and handle them yourself
            ], [
                'optionProvider' => new \EbayVendor\League\OAuth2\Client\OptionProvider\HttpBasicAuthOptionProvider(),
            ]);

            $accessToken = $provider->getAccessToken('authorization_code', [
                'code' => $code,
            ]);

            $response->setSuccess(true);
            $response->setAccessToken($accessToken->getToken());
            $response->setRefreshToken($accessToken->getRefreshToken());
        } catch (Exception $e) {
            $response->setSuccess(false);
            $response->setError($e->getMessage());
        } catch (Throwable $e) {// Throwable exists from php 7
            $response->setSuccess(false);
            $response->setError($e->getMessage());
        }

        return $response;
    }
}
