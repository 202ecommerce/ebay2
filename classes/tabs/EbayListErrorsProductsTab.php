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
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayListErrorsProductsTab extends EbayTab
{

    public function getContent($id_ebay_profile)
    {
        $tasks =  EbayTaskManager::getErrors($id_ebay_profile);

        $ebay_profile = new EbayProfile($id_ebay_profile);
        $vars = array();
        $error = '';
        $context = Context::getContext();
        $link = $context->link;
        $vars['out_of_stock'] = ($ebay_profile->getConfiguration('EBAY_OUT_OF_STOCK')?0:1);
        if (!empty($tasks)) {
        foreach ($tasks as $task){
            $context = Context::getContext();
            $item_id = EbayProduct::getIdProductRef($task['id_product'], $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id, $task['id_product_attribute']);

            $product = new Product($task['id_product'],false, $ebay_profile->id_lang);
            $name_attribute = '';
            if ($task['id_product_attribute'] != 0) {
                $combinations = $product->getAttributeCombinationsById($task['id_product_attribute'], $ebay_profile->id_lang);
                foreach ($combinations as $combination){
                    $name_attribute .= $combination['group_name'] .'-'.$combination['attribute_name'].' ';
                }
            }
            switch ($task['id_task']) {
                case 10:
                    $error = 'Publication refusée';
                    break;
                case 11:
                    $error = 'Mise à jour refusée';
                    break;
                case 13:
                    $error = 'Mise à jour du stock refusée';
                    break;
                case 14:
                    $error = 'Supprimant refusée';
                    break;
            }
            $id =  $task['id_product'].(($task['id_product_attribute'] != 0)?'_'.$task['id_product_attribute']:'');
            $desc = $task['error_code'].' : '.$task['error'];

            $vars['task_errors'][] =  array(
                'date' => $task['date_upd'],
                'name' => $product->name,
                'id_product' => $id,
                'real_id' => $task['id_product'],
                'declinason' => $name_attribute,
                'id_item' => ($item_id)?$item_id: null,
                'error' => $error,
                'error_code' => $task['error_code'],
                'ps_version' => _PS_VERSION_,
                'lang_iso' => $context->language->iso_code,
                'desc_error' => $desc,
                'product_url' => (method_exists($link, 'getAdminLink') ? ($link->getAdminLink('AdminProducts').'&id_product='.(int) $product->id.'&updateproduct') : $link->getProductLink((int) $product->id)),
            );
        }
            $vars['id_ebay_profile'] = $id_ebay_profile;
            $vars['ebay_token'] = Configuration::get('EBAY_SECURITY_TOKEN');
        }

        return $this->display('table_products_errors.tpl', $vars);
    }
}
