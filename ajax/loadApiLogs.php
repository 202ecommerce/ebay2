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

if (!defined('TMP_DS')) {
    define('TMP_DS', DIRECTORY_SEPARATOR);
}

require_once dirname(__FILE__).TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'..'.TMP_DS.'config'.TMP_DS.'config.inc.php';
include_once dirname(__FILE__).'/../../../init.php';
include_once dirname(__FILE__).'/../ebay.php';

class EbayLoadAPILogs extends EbayLoadLogs
{

    protected $file = '/views/templates/hook/table_api_logs.tpl';
    protected function getDatas($offset, $nb_results)
    {
        $logs = EbayApiLog::get($offset, $nb_results);
        foreach ($logs as &$log) {
            $log['data_sent'] = nl2br(TotFormat::prettyPrint($log['data_sent']));
            $log['response'] = nl2br(TotFormat::prettyPrint($log['response']));
        }
        return $logs;
    }
}

$logs = new EbayLoadAPILogs();
echo $logs->getLogs();
