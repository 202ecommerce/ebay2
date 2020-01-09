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
 * @copyright 2007-2020 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 * International Registered Trademark & Property of PrestaShop SA
 */

require_once dirname(__FILE__).'/../EbayProfile.php';
require_once dirname(__FILE__).'/EbayTab.php';

class EbayApiLogsTab extends EbayTab
{
    public function getContent($id_profile, $page_current = 1, $length = 20, $search = false)
    {
        $table = _DB_PREFIX_.'ebay_api_log';
        $sql_count_logs = "SELECT COUNT(*) as `count` FROM `$table` WHERE `id_ebay_profile` = $id_profile";
        $res_count_logs = DB::getInstance()->executeS($sql_count_logs);

        $count_tasks = (int) $res_count_logs[0]['count'];
        $pages_all = ceil($count_tasks/((int) $length));
        $range =3;
        $start = $page_current - $range;
        if ($start <= 0) {
            $start = 1;
        }

        $stop = $page_current + $range;

        if ($stop>$pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int) $page_current - 1;
        $next_page = (int) $page_current + 1;
        $tpl_include = _PS_MODULE_DIR_.'ebay/views/templates/hook/pagination.tpl';
        $limit = $length;
        $offset = (int) $length * ( (int) $page_current - 1 );
        $this->smarty->assign(array('search' => array(
            'id_product' =>  isset($search['id_product'])?$search['id_product']:'',
            'id_product_attribute' =>  isset($search['id_product_attribute'])?$search['id_product_attribute']:'',
            'status' => isset($search['status'])?$search['status']:'',
            'type' => isset($search['type'])?$search['type']:'',
            'date' => isset($search['date'])?$search['date']:'',
        )));


        $logs = EbayApiLog::getApilogs($id_profile, $page_current, $length, $search);
        $this->smarty->assign(array('logs' => $logs));
        $this->smarty->assign(array(
            'prev_page'               => $prev_page,
            'next_page'               => $next_page,
            'tpl_include'             => $tpl_include,
            'pages_all'               => $pages_all,
            'page_current'            => $page_current,
            'start'                   => $start,
            'stop'                    => $stop,
            'apilogs_ajax_link' => $this->context->link->getAdminLink('AdminEbayApiLog'),
        ));
        return $this->display('ebay_log_api.tpl', array());
    }
}
