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

class AdminEbayApiLogController extends ModuleAdminController
{
    public function ajaxProcessdeleteApiLogs()
    {
        $ids_logs = Tools::getValue('ids_logs');
        if ($ids_logs == 'all') {
            DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_api_log");
        } else {
            $ids_logs = pSQL(implode(', ', $ids_logs));
            DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_api_log WHERE id_ebay_api_log IN ($ids_logs)");
        }
        die();
    }

    public function ajaxProcessPaginationLogApi()
    {
        $id_ebay_profile = Tools::getValue('profile');
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;
        $ebay = new Ebay();
        $context = Context::getContext();

        $search = array(
            'id_product' =>  Tools::getValue('id_prod'),
            'id_product_attribute' =>  Tools::getValue('id_prod_attr'),
            'status' => Tools::getValue('status'),
            'type' => Tools::getValue('type'),
            'date' => Tools::getValue('date'),
        );

        $logApiTab = new EbayApiLogsTab($ebay, $context->smarty, $context);
        $response = $logApiTab->getContent($id_ebay_profile, $page_current, $length, $search);
        die($response);
    }

    public function ajaxProcessgetApiLogDetails()
    {
        $id_log = Tools::getValue('id_log');
        if ($id_log) {
            $details = DB::getInstance()->ExecuteS("SELECT * FROM "._DB_PREFIX_."ebay_api_log WHERE id_ebay_api_log =". (int)$id_log);
            die(Tools::jsonEncode($details[0]));
        }
        die();
    }

    public function ajaxProcessDeleteTasks()
    {
        $ids_tasks = Tools::getValue('ids_tasks');
        if ($ids_tasks == 'all') {
            if (Tools::getValue('type') == 'work') {
                DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager WHERE `locked` != 0");
            } else {
                DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager");
            }
        } else {
            $ids_tasks = pSQL(implode(', ', $ids_tasks));
            DB::getInstance()->Execute("DELETE FROM "._DB_PREFIX_."ebay_task_manager WHERE id IN ($ids_tasks)");
        }

        die();
    }

    public function ajaxProcessPaginationLogWorkerTab()
    {
        $id_ebay_profile = Tools::getValue('profile');
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;
        $ebay = new Ebay();
        $context = Context::getContext();
        $logJobsTab = new EbayLogWorkersTab($ebay, $context->smarty, $context);
        $response = $logJobsTab->getContent($id_ebay_profile, $page_current, $length);

        die($response);
    }

    public function ajaxProcessPaginationLogJobsTab()
    {
        $id_ebay_profile = Tools::getValue('profile');
        $page_current = Tools::getValue('page') ? Tools::getValue('page') : 1;
        $length = Tools::getValue('length') ? Tools::getValue('length') : 20;
        $ebay = new Ebay();
        $context = Context::getContext();
        $logJobsTab = new EbayLogJobsTab($ebay, $context->smarty, $context);
        $response = $logJobsTab->getContent($id_ebay_profile, $page_current, $length);

        die($response);
    }
}
