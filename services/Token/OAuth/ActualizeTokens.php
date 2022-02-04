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
 *
 */

namespace Ebay\services\Token\OAuth;

use Common;
use Configuration;
use ProfileConf;
use Symfony\Component\VarDumper\VarDumper;
use EbayProfile;

class ActualizeTokens
{
    public function execute()
    {
        $lastActualize = Configuration::get(Common::LAST_ACTUALIZE_TOKENS);

        if (false == $lastActualize) {
            $lastActualize = date('Y-m-d\TH:i:s');
            Configuration::updateValue(Common::LAST_ACTUALIZE_TOKENS, $lastActualize);
        }

        if ($lastActualize > date('Y-m-d\TH:i:s', strtotime('-2 hours'))) {
            return;
        }

        $profiles = EbayProfile::getAllProfile();

        if (empty($profiles)) {
            return;
        }

        foreach ($profiles as $profile) {
            $ebayProfileObj = new EbayProfile();
            $ebayProfileObj->hydrate($profile);

            $this->actualizeByEbayProfile($ebayProfileObj);
        }

        Configuration::updateValue(Common::LAST_ACTUALIZE_TOKENS, date('Y-m-d\TH:i:s'));
    }

    protected function actualizeByEbayProfile(EbayProfile $ebayProfile)
    {
        $refreshToken = $this->initRefreshAccessToken();
        $token = $refreshToken->refresh($ebayProfile);

        if ($token->isSuccess() == false) {
            return;
        }

        $ebayProfile->setConfiguration(ProfileConf::USER_AUTH_TOKEN, $token->getAccessToken());
    }

    protected function initRefreshAccessToken()
    {
        return new \Ebay\services\Token\OAuth\RefreshAccessToken();
    }
}