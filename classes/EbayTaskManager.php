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
 * @copyright 2007-2017 PrestaShop SA
 * @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
 *  International Registered Trademark & Property of PrestaShop SA
 */


class EbayTaskManager
{
    protected $taskDefinition = array(
        10 => array(
            'function' => 'addfixedPriceItem'
        ),
        11 => array(
            'function' => 'reviseFixedPriceItemData'
        ),
        13 => array(
            'function' => 'reviseFixedPriceItemStock'
        ),
        14 => array(
            'function' => 'endProductOnEbay'
        ),
    );

    public function __construct()
    {

    }

    public static function addTask($type, $product, $id_employee = null, $id_ebay_profile = null, $id_product_attribute = null)
    {

        $ebay_profiles = EbayProfile::getProfilesByIdShop();
        $context = Context::getContext();
        if(isset($id_ebay_profile)){
            $ebay_profiles = EbayProfile::getProfilesById($id_ebay_profile);
        }
        if(!isset($context->employee) && isset($id_employee)){
            $context->employee = new Employee($id_employee);
        }

        if ($type == 'end') {

            foreach ($ebay_profiles as $profile) {
                $ebay_profile = new EbayProfile($profile['id_ebay_profile']);
                if ($item_id = EbayProduct::getIdProductRef($product->id, $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id)) {
                    if (isset($id_product_attribute)) {
                        self::insertTask($product->id, $id_product_attribute, 14, $profile['id_ebay_profile']);
                    } else {
                        self::insertTask($product->id, 0, 14, $profile['id_ebay_profile']);
                    }
                }
            }
            return true;
        }
        if(!$product->active){
            self::deleteTaskForPorduct($product->id);
            return true;
        }

        if (isset($product->id)) {
            $sql = array();
            foreach ($ebay_profiles as $profile) {
                $sql[] = 'SELECT `id_product`, ' . $profile['id_ebay_profile'] . ' AS `id_ebay_profile`, ' . $profile['id_lang'] . ' AS `id_lang`
            FROM `' . _DB_PREFIX_ . 'product`
            WHERE `id_product` = ' . $product->id . '
            AND `active` = 1
            AND `id_category_default` IN
            (' . EbayCategoryConfiguration::getCategoriesQuery(new EbayProfile($profile['id_ebay_profile'])) . ')';
            }


            foreach ($sql as $q) {
                if ($products_ebay = Db::getInstance()->executeS($q)) {

                    foreach ($products_ebay as $product_ebay) {

                        $ebay_profile = new EbayProfile($product_ebay['id_ebay_profile']);

                        $id_attributes = array();

                        $ebay_category = EbaySynchronizer::__getEbayCategory($product->id_category_default, $ebay_profile);
                        $variations = EbaySynchronizer::__loadVariations($product, $ebay_profile, $context, $ebay_category);

                        if (count($variations) && !EbaySynchronizer::__isProductMultiSku($ebay_category, $product->id, $product_ebay['id_lang'], $ebay_profile->ebay_site_id)) {
                            foreach ($variations as $variation) {
                                $id_attributes[] = $variation['id_attribute'];
                            }
                        } else {
                            $id_attributes[] = 0;
                        }

                            foreach ($id_attributes as $id_attribute) {
                                $id_tasks = array(10);
                                if ($item_id = EbayProduct::getIdProductRef($product->id, $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id, $id_attribute)) {
                                    $id_tasks = array(13, 11);
                                    if ($type == 'stock') {
                                        $id_tasks = array(13);
                                    }
                                }
                                foreach ($id_tasks as $id_task) {

                                self::insertTask($product_ebay['id_product'], $id_attribute, $id_task, $product_ebay['id_ebay_profile']);

                            }
                        }
                    }
                }
            }
        }
    }

    public static function insertTask($id_product, $id_product_atttibute, $id_task, $id_ebay_profile)
    {
        $vars = array(
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_atttibute,
            'id_task' => $id_task,
            'id_ebay_profile' => $id_ebay_profile,
        );

        if ($id_task == 14) {
            self::deleteTaskForPorduct($id_product);
        }
        self::deleteErrorsForProduct($id_product);
        if (!self::taskExist($id_product, $id_product_atttibute, $id_task, $id_ebay_profile)) {

            Db::getInstance()->insert('ebay_task_manager', $vars);
        }
    }

    public static function deleteTaskForPorduct($id_product)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 14');

    }

    public static function deleteTaskForPorductAndEbayProfile($id_product, $id_ebay_profile)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 14  AND `id_ebay_profile` = '. (int)$id_ebay_profile);

    }

    public static function deleteTaskForOutOfStock($id_product, $id_ebay_profile)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 13  AND `id_ebay_profile` = '. (int)$id_ebay_profile);

    }

    public static function taskExist($id_product, $id_product_atttibute, $id_task, $id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND
         `id_product_attribute` = ' . (int)$id_product_atttibute . ' AND `id_task` = ' . (int)$id_task . ' AND `id_ebay_profile` = ' . (int)$id_ebay_profile);
    }

    public static function toJob()
    {
        $tasks = self::getJob();
        Configuration::updateValue('DATE_LAST_SYNC_PRODUCTS', date('Y-m-d H:i:s'));
        if ($tasks) {
            while ($row = $tasks->fetch(PDO::FETCH_ASSOC)) {
                $response = EbaySynchronizer::callFunction(self::getMethode($row['id_task']), array('id_product' => $row['id_product'], 'id_product_attribute' => $row['id_product_attribute'], 'id_ebay_profile' => $row['id_ebay_profile']));
                self::checkError($response, $row);
            }
        }
    }

    public static function getJob()
    {
        $unidId = uniqid();
        if (DB::getInstance()->update('ebay_task_manager', array('locked' => $unidId), '`locked` = 0', 1000)) {
            return DB::getInstance()->query('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager  ORDER BY `date_add`');
        }
        return false;
    }

    public static function getMethode($id_task)
    {
        $list_task = new EbayTaskManager();
        $task =$list_task->taskDefinition[$id_task];
        return $task['function'];
    }

    public static function checkError($response, $row)
    {
        if (isset($response->Errors) && isset($response->Ack) && (string)$response->Ack != 'Success' && (string)$response->Ack != 'Warning') {
            foreach ($response->Errors as $e) {

                if ($e->SeverityCode == 'Error') {
                    $msg = (string)$e->LongMessage;
                    if (isset($e->ErrorParameters->Value)) {
                        $msg .= '(' . (string)$e->ErrorParameters->Value . ')';
                    }
                    $date = date('Y-m-d H:i:s');
                    $vars = array(
                        'error_code' => $e->ErrorCode,
                        'error' => pSQL($msg),
                        'retry' => $row['retry'] + 1,
                        'locked' => 0,
                        'date_upd' => pSQL($date),
                    );

                    DB::getInstance()->update('ebay_task_manager', $vars, '`id` = ' . $row['id']);
                }
            }
        } else {
            Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id` = ' . (int)$row['id']);
        }
    }

    public static function getErrors($id_ebay_profile) {

        return DB::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` IS NOT NULL and `id_ebay_profile` = '.(int)$id_ebay_profile.' ORDER BY `date_add`');
    }
    public static function deleteErrorsForProduct($id_product) {

        return DB::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` IS NOT NULL and `id_product` = '.(int)$id_product);
    }

    public static function getNbTasks($id_ebay_profile) {

        $tasks = Db::getInstance()->executeS('SELECT COUNT(*) AS nb	FROM '._DB_PREFIX_.'ebay_task_manager WHERE `error_code` IS NULL and `id_ebay_profile` = '.(int)$id_ebay_profile.' GROUP BY `id_product_attribute`, `id_product` ');
        return count($tasks);
    }
}