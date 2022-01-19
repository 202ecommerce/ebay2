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

use Ebay\services\Token\OAuth\AccountConfiguration;
use Ebay\classes\SandboxMode;
use Ebay\services\Token\OAuth\GetAccessToken;

class AdminTokenListenerController extends ModuleAdminController
{
    public function run()
    {
        parent::init();
        $this->handleEbayInfo();
        $this->doRedirection();
    }

    public function handleEbayInfo()
    {
        $code = Tools::getValue('code');
        $accountConfiguration = $this->initAccountConfiguration();
        $getAccessToken = $this->initGetAccessToken();

        $accountConfiguration->setAppId($this->module->ebay_profile->getConfiguration(ProfileConf::APP_ID));
        $accountConfiguration->setCertId($this->module->ebay_profile->getConfiguration(ProfileConf::CERT_ID));
        $accountConfiguration->setRuName($this->module->ebay_profile->getConfiguration(ProfileConf::RU_NAME));
        $accountConfiguration->setSandbox((new SandboxMode())->isSandbox());

        $response = $getAccessToken->get($accountConfiguration, $code);

        if ($response->isSuccess() == false) {
            PrestaShopLogger::addLog('[ebay] Fail generate access token. ' . $response->getError());
            return;
        }

        $this->module->ebay_profile->setConfiguration(ProfileConf::USER_AUTH_TOKEN, $response->getAccessToken());
        $this->module->ebay_profile->setConfiguration(ProfileConf::REFRESH_TOKEN, $response->getRefreshToken());
    }

    public function initAccountConfiguration()
    {
        return new AccountConfiguration();
    }

    public function doRedirection()
    {
        Tools::redirectAdmin(
            $this->context->link->getAdminLink(
                'AdminModules',
                true,
                [],
                [
                    'configure' => 'ebay'
                ]
            )
        );
    }

    public function initGetAccessToken()
    {
        return new GetAccessToken();
    }
}