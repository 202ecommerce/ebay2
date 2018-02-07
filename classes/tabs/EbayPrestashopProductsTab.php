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
 * @author    PrestaShop SA <contact@prestashop.com>
 * @copyright 2007-2018 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

class EbayPrestashopProductsTab extends EbayTab
{
    public function getContent()
    {

        $controller = Tools::getValue('controller');
        $configure = Tools::getValue('configure');
        $token = Tools::getValue('token');
        $tab_module = Tools::getValue('tab_module');
        $module_name = Tools::getValue('module_name');

        $show_products_url = 'index.php?controller='.urlencode($controller).
            '&configure='.urlencode($configure).'&token='.urlencode($token).
            '&tab_module='.urlencode($tab_module).
            '&module_name='.urlencode($module_name).
            '&id_tab=15&section=products';

        // Smarty
        $template_vars = array(
            'module_dir' => _MODULE_DIR_,
            'id_ebay_profile'         => $this->ebay_profile->id,
            'ebay_sync_option_resync' => $this->ebay_profile->getConfiguration('EBAY_SYNC_OPTION_RESYNC'),
            'show_products_url'       => $show_products_url,
            'id_employee'             => Context::getContext()->employee->id,
            'admin_path'      => basename(_PS_ADMIN_DIR_),
        );

        return $this->display('prestashop_products.tpl', $template_vars);
    }
}
