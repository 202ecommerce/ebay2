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
        if (isset($id_ebay_profile)) {
            $ebay_profiles = EbayProfile::getProfilesById($id_ebay_profile);
        }
        if (!isset($context->employee) && isset($id_employee)) {
            $context->employee = new Employee($id_employee);
        }

        if ($type == 'end') {
            foreach ($ebay_profiles as $profile) {
                $ebay_profile = new EbayProfile($profile['id_ebay_profile']);
                if ($item_id = EbayProduct::getIdProductRef($product->id, $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id, false, $ebay_profile->id_shop)) {
                    if (isset($id_product_attribute)) {
                        self::insertTask($product->id, $id_product_attribute, 14, $profile['id_ebay_profile']);
                    } else {
                        self::insertTask($product->id, 0, 14, $profile['id_ebay_profile']);
                    }
                }
            }

            return true;
        }
        if (!$product->active) {
            self::deleteTaskForPorduct($product->id);
            return true;
        }



        if (isset($product->id)) {
            $sql = array();
            foreach ($ebay_profiles as $profile) {
                $id_shop = $profile['id_shop'];
                $sql[] = 'SELECT p.id_product, ' . $profile['id_ebay_profile'] . ' AS `id_ebay_profile`, ' . $profile['id_lang'] . ' AS `id_lang`
            FROM `' . _DB_PREFIX_ . 'product` p
	        LEFT JOIN '._DB_PREFIX_.'product_shop ps ON p.id_product = ps.id_product
            WHERE p.id_product = ' . $product->id . '
            AND ps.active = 1 AND ps.id_shop = '.$id_shop.'
            AND ps.id_category_default IN
            (' . EbayCategoryConfiguration::getCategoriesQuery(new EbayProfile($profile['id_ebay_profile'])) . ')';
            }

            foreach ($sql as $q) {
                if ($products_ebay = Db::getInstance()->executeS($q)) {
                    foreach ($products_ebay as $product_ebay) {
                        if (EbayProductConfiguration::isblocked($product_ebay['id_ebay_profile'], $product->id)) {
                            continue;
                        }

                        $ebay_profile = new EbayProfile($product_ebay['id_ebay_profile']);
                        $restrictSync = (int) $ebay_profile->getConfiguration('RESTRICT_SYNC');
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
                            $ebay_product = EbayProduct::getProductById($product->id, $ebay_profile->ebay_user_identifier, $ebay_profile->ebay_site_id, $id_attribute, $ebay_profile->id_shop);
                            if (isset($ebay_product['id_product']) && $ebay_product['id_product']) {
                                if ($restrictSync) {
                                    $id_tasks = array(13);
                                } else {
                                    $id_tasks = array(13, 11);
                                }

                                if ($type == 'stock') {
                                    $id_tasks = array(13);
                                }
                                if (StockAvailable::getQuantityAvailableByProduct($product->id, $id_attribute, $ebay_profile->id_shop) <= 0 &&
                                    !(bool)$ebay_profile->getConfiguration('EBAY_OUT_OF_STOCK')) {
                                    $id_tasks = array(14);
                                }
                            }

                            if (in_array(10, $id_tasks) && StockAvailable::getQuantityAvailableByProduct($product->id, $id_attribute, $ebay_profile->id_shop) == 0) {
                                continue;
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

    public static function insertTask($id_product, $id_product_atttibute, $id_task, $id_ebay_profile, $ingnore = false)
    {
        $priority = 0;
        switch ($id_task) {
            case 14:
                $priority = Ebay::PRIORITY_DELETE_PRODUCT;
                break;
            case 13:
                $priority = Ebay::PRIORITY_UPDATE_STOCK;
                break;
            case 11:
                $priority = Ebay::PRIORITY_UPDATE_PRODUCT;
                break;
            case 10:
                $priority = Ebay::PRIORITY_CREATE_PRODUCT;
                break;
        }
        $vars = array(
            'id_product' => $id_product,
            'id_product_attribute' => $id_product_atttibute,
            'id_task' => $id_task,
            'id_ebay_profile' => $id_ebay_profile,
            'date_add' =>  pSQL(date("Y-m-d H:i:s")),
            'retry' => 0,
            'locked' => 0,
            'priority' => $priority,
        );

        if ($id_task == 14) {
            self::deleteTaskForPorductAndEbayProfile($id_product, $id_ebay_profile, $id_product_atttibute);
        }
        self::deleteErrorsForProduct($id_product);
        if (!self::taskExist($id_product, $id_product_atttibute, $id_task, $id_ebay_profile) || $ingnore) {
            Db::getInstance()->insert('ebay_task_manager', $vars, false, true, DB::INSERT_IGNORE);
        }
    }

    public static function deleteTaskForPorduct($id_product)
    {
        return Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 14');
    }

    public static function deleteTaskForPorductAndEbayProfile($id_product, $id_ebay_profile, $id_product_attribute = 0)
    {
        if ($id_product_attribute != 0) {
            $result = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 14  AND `id_ebay_profile` = '. (int)$id_ebay_profile).' AND `id_product_attribute`='.$id_product_attribute;

            return $result;
        } else {
            $result = Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `id_product` = ' . (int)$id_product . ' AND `id_task` != 14  AND `id_ebay_profile` = '. (int)$id_ebay_profile);
            
            return $result;
        }
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
        self::cleanTasks();
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
        if (self::nbTaskInwork() < 1500) {
            $unidId = uniqid();
            if (DB::getInstance()->update('ebay_task_manager', array('locked' => $unidId), '`locked` = 0 AND `retry` = 0 ORDER BY `priority`', 150)) {
                return DB::getInstance()->query('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `locked` = "' . $unidId . '"  ORDER BY `priority`');
            }
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

    public static function cleanTasks()
    {
        return DB::getInstance()->update('ebay_task_manager', array('locked' => 0, 'error_code' => null, 'error' => ''), '`locked` != 0 AND `date_add` <  NOW() - INTERVAL 120 MINUTE');
    }

    public static function getCountErrors($id_ebay_profile, $search = false, $id_lang = null)
    {
        if ($search && $id_lang) {
            $profile = new EbayProfile($id_ebay_profile);
            $query = "SELECT COUNT(*) as `count` FROM "._DB_PREFIX_."ebay_task_manager etm
                       LEFT JOIN "._DB_PREFIX_."product_lang pl ON etm.id_product = pl.id_product AND pl.id_lang = ".(int) $id_lang." AND pl.id_shop = ".(int) $profile->id_shop."
                       WHERE etm.error_code NOT IN('0') AND etm.error_code IS NOT NULL AND etm.id_ebay_profile = ".(int) $id_ebay_profile;
            if ($search['id_product']) {
                $query .= " AND pl.id_product LIKE '%".(int) $search['id_product']."%'";
            }
            if ($search['name_product']) {
                $query .= " AND pl.name LIKE '%".pSQL($search['name_product'])."%'";
            }

            $result = DB::getInstance()->ExecuteS($query);
            return (int) $result[0]['count'];
        } else {
            $res_sql = DB::getInstance()->executeS('SELECT COUNT(*) as `count` FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` IS NOT NULL and `id_ebay_profile` = '.(int)$id_ebay_profile);
            return (int) $res_sql[0]['count'];
        }
    }

    public static function getErrors($id_ebay_profile, $page_current = false, $length = false, $search = false, $id_lang = null)
    {
        $profile = new EbayProfile($id_ebay_profile);
        if ($page_current && $length && $id_lang) {
            $limit = (int) $length;
            $offset = $limit * ( (int) $page_current - 1 );
            $query = "SELECT * FROM "._DB_PREFIX_."ebay_task_manager etm
                       LEFT JOIN "._DB_PREFIX_."product_lang pl ON etm.id_product = pl.id_product AND pl.id_lang = ".(int) $id_lang." AND pl.id_shop = ".(int) $profile->id_shop."
                       WHERE etm.error_code NOT IN('0') AND etm.error_code IS NOT NULL AND etm.id_ebay_profile = ".(int) $id_ebay_profile;
            if (false == empty($search['id_product'])) {
                $query .= " AND pl.id_product LIKE '%".(int) $search['id_product']."%'";
            }
            if (false == empty($search['name_product'])) {
                $query .= " AND pl.name LIKE '%".pSQL($search['name_product'])."%'";
            }
            $query .=  " ORDER BY `date_add` LIMIT ".pSQL($limit)."  OFFSET ".(int) $offset;
            //var_dump($query); die();
            return DB::getInstance()->ExecuteS($query);
        }

        return DB::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` IS NOT NULL and `id_ebay_profile` = '.(int)$id_ebay_profile.' ORDER BY `date_add`');
    }

    public static function getErrorsCount($id_ebay_profile)
    {

        return DB::getInstance()->executeS('SELECT COUNT(*) AS nb FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE error_code NOT IN(\'0\') AND `error_code` IS NOT NULL and `id_ebay_profile` = '.(int)$id_ebay_profile.' ORDER BY `date_add`');
    }

    public static function deleteErrorsForProduct($id_product)
    {

        return DB::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` IS NOT NULL and `id_product` = '.(int)$id_product);
    }

    public static function getNbTasks($id_ebay_profile)
    {
        $tasks = Db::getInstance()->executeS('SELECT COUNT(*) AS nb	FROM '._DB_PREFIX_.'ebay_task_manager WHERE (`error_code` = \'0\' OR `error_code` IS NULL) and `id_ebay_profile` = '.(int)$id_ebay_profile.' GROUP BY `id_product_attribute`, `id_product` ');
        return count($tasks);
    }

    public static function getNbTasksTotal()
    {
        $tasks = Db::getInstance()->executeS('SELECT COUNT(*) AS nb	FROM '._DB_PREFIX_.'ebay_task_manager WHERE (`error_code` = \'0\' OR `error_code` IS NULL) GROUP BY `id_product_attribute`, `id_product` ');
        return count($tasks);
    }

    public static function deleteErrors($id_ebay_profile)
    {
        $sql = 'UPDATE `'._DB_PREFIX_.'ebay_task_manager`
										SET `error_code` = null, `error` = "", `retry` = 0
										WHERE `id_ebay_profile` = '.(int)$id_ebay_profile;
        return  Db::getInstance()->execute($sql);
    }

    public static function nbTaskInwork()
    {
        $sql_select = "SELECT COUNT(*) AS nb  FROM `"._DB_PREFIX_."ebay_task_manager` WHERE `locked` != 0";
        $res_select = DB::getInstance()->executeS($sql_select);
        return $res_select[0]['nb'];
    }

    public static function getTaskByErrorCode($error_code, $id_ebay_profile)
    {
        return DB::getInstance()->executeS('SELECT * FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` ='.pSQL($error_code).' AND `id_ebay_profile` = '.(int)$id_ebay_profile.' ORDER BY `date_add`');
    }
}
