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
class AdminEbayProductExcluController extends ModuleAdminController
{
    public function ajaxProcessGetProductExclu()
    {
        $ebay = Module::getInstanceByName('ebay');
        exit($ebay->displayProductExclu(Tools::getValue('id_employee')));
    }

    public function ajaxProcessInclureProduct()
    {
        EbayProductConfiguration::insertOrUpdate(Tools::getValue('id_product'), [
            'id_ebay_profile' => Tools::getValue('id_ebay_profile'),
            'blacklisted' => 0,
            'extra_images' => 0,
        ]);
        $product = new Product(Tools::getValue('id_product'));
        EbayTaskManager::addTask('mod', $product, Tools::getValue('id_employee'), Tools::getValue('id_ebay_profile'));
    }
}
