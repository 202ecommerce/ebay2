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
 * @author 202-ecommerce <tech@202-ecommerce.com>
 * @copyright Copyright (c) 2017-2020 202-ecommerce
 * @license Commercial license
 *  International Registered Trademark & Property of PrestaShop SA
 */

class EbayListErrorsProductsTab extends EbayTab
{

    public function getContent($id_ebay_profile, $page_current = 1, $length = 20, $token_for_product = false, $search = false)
    {

        $ebay_profile = new EbayProfile($id_ebay_profile);
        $count_product_errors = EbayTaskManager::getCountErrors($id_ebay_profile, $search, $ebay_profile->id_lang);
        $pages_all = ceil((int)$count_product_errors / (int) $length);
        $range =3;
        $start = (int) $page_current - $range;
        if ($start <= 0) {
            $start = 1;
        }

        $stop = (int) $page_current + $range;

        if ($stop>$pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int) $page_current - 1;
        $next_page = (int) $page_current + 1;
        $tpl_include = _PS_MODULE_DIR_.'ebay/views/templates/hook/pagination.tpl';

        $tasks = EbayTaskManager::getErrors($id_ebay_profile, $page_current, $length, $search, $ebay_profile->id_lang);


        $vars = array();
        $error = '';
        $context = Context::getContext();
        $link = $context->link;
        $vars['out_of_stock'] = ($ebay_profile->getConfiguration('EBAY_OUT_OF_STOCK') ? 0 : 1);
        if (!empty($tasks)) {
            foreach ($tasks as $task) {
                $item_id = EbayProduct::getIdProductRef($task['id_product'], $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id, $task['id_product_attribute'], $ebay_profile->id_shop);

                $product = new Product($task['id_product'], false, $ebay_profile->id_lang);
                $name_attribute = '';
                if ($task['id_product_attribute'] != 0) {
                    $combinations = $product->getAttributeCombinationsById($task['id_product_attribute'], $ebay_profile->id_lang);
                    foreach ($combinations as $combination) {
                        $name_attribute .= $combination['group_name'] . '-' . $combination['attribute_name'] . ' ';
                    }
                }
                switch ($task['id_task']) {
                    case 10:
                        $error = $this->ebay->l('Publication refusée');
                        break;
                    case 11:
                        $error = $this->ebay->l('Mise à jour refusée');
                        break;
                    case 13:
                        $error = $this->ebay->l('Mise à jour du stock refusée');
                        break;
                    case 14:
                        $error = $this->ebay->l('Supprimant refusée');
                        break;
                }

                $id = $task['id_product'] . (($task['id_product_attribute'] != 0) ? '_' . $task['id_product_attribute'] : '');
                $desc = $task['error_code'] . ' : ' . $task['error'];
                $name_product = $product->name;
                if ((int) $task['id_product_attribute'] > 0) {
                    $combinaison = $this->ebay->_getAttributeCombinationsByIds($product, (int) $task['id_product_attribute'], $ebay_profile->id_lang);
                    $combinaison = $combinaison[0];
                    $variation_specifics = EbaySynchronizer::__getVariationSpecifics($combinaison['id_product'], $combinaison['id_product_attribute'], $ebay_profile->id_lang, $ebay_profile->ebay_site_id);
                    foreach ($variation_specifics as $variation_specific) {
                        $name_product .= ' '.$variation_specific;
                    }
                }

                if ($token_for_product) {
                    $vars['task_errors'][] = array(
                        'date' => $task['date_upd'],
                        'name' => $name_product,
                        'id_product' => $id,
                        'real_id' => $task['id_product'],
                        'declinason' => $name_attribute,
                        'id_item' => ($item_id) ? $item_id : null,
                        'error' => $error,
                        'error_code' => $task['error_code'],
                        'ps_version' => _PS_VERSION_,
                        'lang_iso' => $context->language->iso_code,
                        'desc_error' => $desc,
                        'product_url' => (method_exists($link, 'getAdminLink') ? ($link->getAdminLink('AdminProducts', false, array('id_product' => (int)$product->id)).'&token='.$token_for_product . '&id_product=' . (int)$product->id . '&updateproduct') : $link->getProductLink((int)$product->id)),
                    );
                } else {
                    $vars['task_errors'][] = array(
                        'date' => $task['date_upd'],
                        'name' => $name_product,
                        'id_product' => $id,
                        'real_id' => $task['id_product'],
                        'declinason' => $name_attribute,
                        'id_item' => ($item_id) ? $item_id : null,
                        'error' => $error,
                        'error_code' => $task['error_code'],
                        'ps_version' => _PS_VERSION_,
                        'lang_iso' => $context->language->iso_code,
                        'desc_error' => $desc,
                        'product_url' => (method_exists($link, 'getAdminLink') ? ($link->getAdminLink('AdminProducts', false, array('id_product' => (int)$product->id)).'&token='.Tools::getAdminTokenLite('AdminProducts') . '&id_product=' . (int)$product->id . '&updateproduct') : $link->getProductLink((int)$product->id)),
                    );
                }
            }

            if ($token_for_product) {
                $vars['token_for_product'] = $token_for_product;
            } else {
                $vars['token_for_product'] = Tools::getAdminTokenLite('AdminProducts');
            }

            $vars['id_ebay_profile'] = $id_ebay_profile;
            $vars['ebay_token'] = Configuration::get('EBAY_SECURITY_TOKEN');
            $data_for_paginaton = array(
                'prev_page'               => $prev_page,
                'next_page'               => $next_page,
                'tpl_include'             => $tpl_include,
                'pages_all'               => $pages_all,
                'page_current'            => $page_current,
                'start'                   => $start,
                'stop'                    => $stop,
            );
            $vars = array_merge($vars, $data_for_paginaton);
        }
        $vars['search'] = $search;
        if ($token_for_product) {
            $vars['token_for_product'] = $token_for_product;
        } else {
            $vars['token_for_product'] = Tools::getAdminTokenLite('AdminProducts');
        }
        $vars['ebayListErrorsProductsController'] = $this->context->link->getAdminLink('AdminEbayListErrorsProducts');
        return $this->display('table_products_errors.tpl', $vars);
    }
}
