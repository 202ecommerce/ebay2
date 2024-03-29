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
require_once dirname(__FILE__) . '/../EbayProfile.php';
require_once dirname(__FILE__) . '/EbayTab.php';

class EbayLogJobsTab extends EbayTab
{
    public function getContent($id_profile, $page_current = 1, $length = 20)
    {
        $table = _DB_PREFIX_ . 'ebay_task_manager';
        $sql_count_tasks = "SELECT COUNT(*) as `count` FROM `$table` WHERE `locked` = 0 AND `retry` = 0 AND `id_ebay_profile` = $id_profile";
        $res_count_tasks = Db::getInstance()->executeS($sql_count_tasks);

        $count_tasks = (int) $res_count_tasks[0]['count'];
        $pages_all = ceil($count_tasks / ((int) $length));
        $range = 3;
        $start = $page_current - $range;
        if ($start <= 0) {
            $start = 1;
        }

        $stop = $page_current + $range;

        if ($stop > $pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int) $page_current - 1;
        $next_page = (int) $page_current + 1;
        $tpl_include = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/pagination.tpl';
        $limit = $length;
        $offset = (int) $length * ((int) $page_current - 1);

        $sql_select = 'SELECT * FROM `' . pSQL($table) . '` WHERE `locked` = 0 AND `retry` = 0 AND  `id_ebay_profile` = ' . pSQL($id_profile) . ' ORDER BY `date_add` LIMIT ' . pSQL($limit) . ' OFFSET ' . pSQL($offset);
        $tasks = Db::getInstance()->executeS($sql_select);
        $this->smarty->assign(['tasks' => $tasks]);
        $this->smarty->assign([
            'prev_page' => $prev_page,
            'next_page' => $next_page,
            'tpl_include' => $tpl_include,
            'pages_all' => $pages_all,
            'page_current' => $page_current,
            'start' => $start,
            'stop' => $stop,
            'apilogs_ajax_link' => $this->context->link->getAdminLink('AdminEbayApiLog'),
        ]);

        return $this->display('ebay_log_jobs.tpl', []);
    }
}
