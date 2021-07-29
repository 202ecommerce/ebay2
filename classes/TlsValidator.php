<?php
/**
 * 2007-2021 PrestaShop
 *
 * NOTICE OF LICENSE
 *
 * This source file is subject to the Academic Free License (AFL 3.0)
 * that is bundled with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://opensource.org/licenses/afl-3.0.php
 * If you did not receive a copy of the license and are unable to
 * obtain it through the world-wide-web, please send an email
 * to license@prestashop.com so we can send you a copy immediately.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade PrestaShop to newer
 * versions in the future. If you wish to customize PrestaShop for your
 * needs please refer to http://www.prestashop.com for more information.
 *
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 2007-2021 202-ecommerce
 * @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

class TlsValidator
{
    public function isValid()
    {
        $curl = curl_init($this->getTlsController());
        curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, true);
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 2);
        // In some environments a const CURL_SSLVERSION_TLSv1_2 can be absent, so we use 6 instead
        curl_setopt($curl, CURLOPT_SSLVERSION, 6);
        curl_exec($curl);
        $info = curl_getinfo($curl);

        if ($info['http_code'] == TlsConf::STATUS_SUCCESS) {
            return true;
        }

        return false;
    }

    public function getTlsController()
    {
        return Context::getContext()->link->getModuleLink('ebay', 'tls');
    }
}