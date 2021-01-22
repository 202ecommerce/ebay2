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

class EbayOrphanListingsTab extends EbayTab
{

    public function getContent()
    {

        $vars = array(
            'ebay_token' => Configuration::get('EBAY_SECURITY_TOKEN'),
            'id_employee' => $this->context->employee->id,
            '_module_dir_' => _MODULE_DIR_,
            'ebayOrphanListingsController' => $this->context->link->getAdminLink('AdminEbayOrphanListings')
        );

        return $this->display('table_orphan_listings_ajax.tpl', $vars);
    }

    public function postProcess()
    {
    }

    /*
     *
     * Get alert to see if some multi variation product on PrestaShop were added to a non multi sku categorie on ebay
     *
     */

    private function _getAlertCategories()
    {
    }
}
