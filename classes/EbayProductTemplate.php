<?php
/**
 * 2007-2017 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2018 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayProductTemplate extends ObjectModel
{
    /**
     * @param Ebay $ebay
     * @param Smarty_Data $smarty
     * @return bool|Smarty_Internal_Template
     */
    public static function getContent($ebay, $smarty)
    {
        $logo_url = (Tools::getShopDomainSsl(true)._PS_IMG_.Configuration::get('PS_LOGO').'?'.Configuration::get('PS_IMG_UPDATE_TIME'));

        $smarty->assign(array(
            'shop_logo' => $logo_url,
            'shop_name' => Configuration::get('PS_SHOP_NAME'),
            'module_url' => self::__getModuleUrl(),
        ));

        return $ebay->display(dirname(__FILE__).'/../ebay.php', 'lib/ebay/ebay.tpl');
    }

    /**
     * Returns the module url
     *
     **/
    protected static function __getModuleUrl()
    {
        return Tools::getShopDomainSsl(true).__PS_BASE_URI__.'modules/ebay/';
    }
}
