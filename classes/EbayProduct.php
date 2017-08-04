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

class EbayProduct
{
    public static function getIdProductRef($id_product, $ebay_identifier, $ebay_site_id, $id_attribute = null, $id_shop = null)
    {
        $query = 'SELECT `id_product_ref`
			FROM `'._DB_PREFIX_.'ebay_product` ep
			INNER JOIN `'._DB_PREFIX_.'ebay_profile` ep1
			ON ep.`id_ebay_profile` = ep1.`id_ebay_profile`
			AND ep1.`ebay_user_identifier` = \''.pSQL($ebay_identifier).'\'
			AND ep1.`ebay_site_id` = '.(int) $ebay_site_id.'
			WHERE ep.`id_product` = '.(int) $id_product;

        if ($id_attribute) {
            $query .= ' AND ep.`id_attribute` = '.(int) $id_attribute;
        }
        if ($id_shop) {
            $query .= ' AND ep1.`id_shop` = '.(int) $id_shop;
        }
        return Db::getInstance()->getValue($query);
    }

    public static function getPercentOfCatalog($ebay_profile)
    {

            $id_shop = $ebay_profile->id_shop;
            $sql = 'SELECT `id_product`
				FROM `'._DB_PREFIX_.'product_shop`
				WHERE `id_shop` = '.(int) $id_shop;


        $results = Db::getInstance()->executeS($sql);
        $id_shop_products = array_map(array('EbayProduct', 'getProductsIdFromTable'), $results);

        $nb_shop_products = count($id_shop_products);

        if ($nb_shop_products) {
            $sql2 = 'SELECT count(*)
				FROM `'._DB_PREFIX_.'ebay_product`
				WHERE `id_product` IN ('.implode(',', $id_shop_products).') GROUP BY `id_product`';
            $nb_synchronized_products = Db::getInstance()->getValue($sql2);
            return number_format($nb_synchronized_products / $nb_shop_products * 100.0, 2);
        } else {
            return '-';
        }
    }

    public static function getProductsIdFromTable($a)
    {
        return (int) $a['id_product'];
    }

    public static function getProductsIdFromItemId($itemID)
    {
        $row = Db::getInstance()->getRow('SELECT id_product, id_attribute, id_ebay_profile
			FROM `'._DB_PREFIX_.'ebay_product`
			WHERE `id_product_ref` = '. $itemID);
        if ((int) $row['id_product']) {
            return array('id_product' => (int) $row['id_product'], 'id_product_attribute' => (int) $row['id_attribute'], 'id_ebay_profile' => (int) $row['id_ebay_profile'] );
        } else {
            return false;
        }
    }

    public static function getNbProducts($id_ebay_profile)
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM `'._DB_PREFIX_.'ebay_product`
			WHERE `id_ebay_profile` = '.(int) $id_ebay_profile);
    }

    public static function getNbProductsByCategory($id_ebay_profile, $id_category)
    {
        return Db::getInstance()->getValue('SELECT count(*)
			FROM `'._DB_PREFIX_.'ebay_product` ep
			INNER JOIN `'._DB_PREFIX_.'product` p
			ON p.`id_product` = ep.`id_product`
			WHERE p.`id_category_default` = '.(int)$id_category .' AND ep.`id_ebay_profile` = '.(int) $id_ebay_profile);
    }

    public static function getNbProductsByIdEbayProfiles($id_ebay_profiles = array())
    {
        $query = 'SELECT `id_ebay_profile`, count(*) AS `nb`
			FROM `'._DB_PREFIX_.'ebay_product`';
        if ($id_ebay_profiles) {
            $query .= ' WHERE `id_ebay_profile` IN ('.implode(',', array_map('intval', $id_ebay_profiles)).')
				GROUP BY `id_ebay_profile`';
        }

        $res = Db::getInstance()->executeS($query);

        $ret = array();
        foreach ($res as $row) {
            $ret[$row['id_ebay_profile']] = $row['nb'];
        }

        return $ret;
    }

    public static function getProducts($not_update_for_days, $limit)
    {
        return Db::getInstance()->ExecuteS('SELECT ep.id_product_ref, ep.id_product
			FROM '._DB_PREFIX_.'ebay_product AS ep
			WHERE NOW() > DATE_ADD(ep.date_upd, INTERVAL '.(int) $not_update_for_days.' DAY)
			LIMIT '.(int) $limit);
    }

    public static function insert($data)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        return $dbEbay->autoExecute(_DB_PREFIX_.'ebay_product', $data, 'INSERT');
    }

    public static function updateByIdProductRef($id_product_ref, $datas)
    {
        $to_insert = array();
        if (is_array($datas) && count($datas)) {
            foreach ($datas as $key => $data) {
                $to_insert[pSQL($key)] = $data;
            }
        }

        //If eBay Product has been inserted then the configuration of eBay is OK
        Configuration::updateValue('EBAY_CONFIGURATION_OK', true);
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());
        return $dbEbay->autoExecute(_DB_PREFIX_.'ebay_product', $to_insert, 'UPDATE', '`id_product_ref` = "'.pSQL($id_product_ref).'"');
    }

    public static function updateByIdProduct($id_product, $datas, $id_ebay_profile, $id_attribute = 0)
    {
        $to_insert = array();
        if (is_array($datas) && count($datas)) {
            foreach ($datas as $key => $data) {
                $to_insert[pSQL($key)] = $data;
            }
        }

        //If eBay Product has been inserted then the configuration of eBay is OK
        Configuration::updateValue('EBAY_CONFIGURATION_OK', true);
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());
        return $dbEbay->autoExecute(_DB_PREFIX_.'ebay_product', $to_insert, 'UPDATE', '`id_product` = "'.pSQL($id_product).'" AND `id_ebay_profile` = "'.(int) $id_ebay_profile.'" AND `id_attribute` = "'.(int) $id_attribute.'"');
    }

    public static function deleteByIdProductRef($id_product_ref)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product`
			WHERE `id_product_ref` = \''.pSQL($id_product_ref).'\'');
    }

    public static function deleteByIdProduct($id_product, $id_ebay_profile, $id_attribute = 0)
    {
        return Db::getInstance()->execute('DELETE FROM `'._DB_PREFIX_.'ebay_product`
			WHERE `id_product` = \''.pSQL($id_product).'\' AND `id_attribute` = \''.pSQL($id_attribute).'\' AND `id_ebay_profile` = \''.(int) $id_ebay_profile.'\'');
    }

    public static function getCountProductsWithoutBlacklisted($id_lang, $id_ebay_profile, $no_blacklisted)
    {
        $ebay_profile = new EbayProfile($id_ebay_profile);
        $sql = 'SELECT COUNT(*) as `count`
			FROM (SELECT ep.id_product FROM `'._DB_PREFIX_.'ebay_product` ep
			LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc ON (epc.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $id_lang.' ';

        $sql .= 'AND id_shop = '.(int)$ebay_profile->id_shop;

        $sql .= ') WHERE ep.`id_ebay_profile` = '.(int) $id_ebay_profile;
        if ($no_blacklisted) {
            $sql .= ' AND (epc.`blacklisted` = 0 OR epc.`blacklisted` IS NULL)';
        }

        $sql .= ' GROUP BY ep.id_product, ep.id_attribute, ep.id_product_ref) AS T';

        return Db::getInstance()->ExecuteS($sql);
    }

    public static function getProductsWithoutBlacklisted($id_lang, $id_ebay_profile, $no_blacklisted, $page_current = null, $length = 20)
    {
        if ($page_current) {
            $offset = ((int) $page_current - 1) * (int) $length;
            $ebay_profile = new EbayProfile($id_ebay_profile);
            $sql = 'SELECT ep.`id_product`, ep.`id_attribute`, ep.`id_product_ref`,
			p.`id_category_default`, p.`reference`, p.`ean13`, p.`upc`,
			pl.`name`, m.`name` as manufacturer_name
			FROM `'._DB_PREFIX_.'ebay_product` ep
			LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc ON (epc.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $id_lang.' ';

            $sql .= 'AND id_shop = '.(int)$ebay_profile->id_shop;

            $sql .= ') WHERE ep.`id_ebay_profile` = '.(int) $id_ebay_profile;
            if ($no_blacklisted) {
                $sql .= ' AND (epc.`blacklisted` = 0 OR epc.`blacklisted` IS NULL)';
            }

            $sql .= ' GROUP BY id_product, id_attribute, id_product_ref';
            $sql .= ' ORDER BY ep.`id_product` LIMIT '.$length.' OFFSET '.$offset;

            return Db::getInstance()->ExecuteS($sql, false);
        } else {
            $ebay_profile = new EbayProfile($id_ebay_profile);
            $sql = 'SELECT ep.`id_product`, ep.`id_attribute`, ep.`id_product_ref`,
			p.`id_category_default`, p.`reference`, p.`ean13`, p.`upc`,
			pl.`name`, m.`name` as manufacturer_name
			FROM `'._DB_PREFIX_.'ebay_product` ep
			LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc ON (epc.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'product` p ON (p.`id_product` = ep.`id_product`)
			LEFT JOIN `'._DB_PREFIX_.'manufacturer` m ON (m.`id_manufacturer` = p.`id_manufacturer`)
			LEFT JOIN `'._DB_PREFIX_.'product_lang` pl ON (pl.`id_product` = p.`id_product` AND pl.`id_lang` = '.(int) $id_lang.' ';

            $sql .= 'AND id_shop = '.(int)$ebay_profile->id_shop;

            $sql .= ') WHERE ep.`id_ebay_profile` = '.(int) $id_ebay_profile;
            if ($no_blacklisted) {
                $sql .= ' AND (epc.`blacklisted` = 0 OR epc.`blacklisted` IS NULL)';
            }

            $sql .= ' GROUP BY id_product, id_attribute, id_product_ref';

            return Db::getInstance()->ExecuteS($sql, false);
        }
    }

    public static function getEbayUrl($reference, $mode_dev = false)
    {
        $ebay_site_id = Db::getInstance()->getValue('SELECT ep.`ebay_site_id`
			FROM `'._DB_PREFIX_.'ebay_profile` ep
			INNER JOIN `'._DB_PREFIX_.'ebay_product` ep1
			ON ep.`id_ebay_profile` = ep1.`id_ebay_profile`
			AND ep1.`id_product_ref` = \''.pSQL($reference).'\'');
        if (!$ebay_site_id) {
            return '';
        }

        $site_extension = EbayCountrySpec::getSiteExtensionBySiteId($ebay_site_id);
        if ($ebay_site_id== 23) {
            return 'http://cgi'.($mode_dev ? '.sandbox' : '').'.befr.ebay.'.$site_extension.'/ws/eBayISAPI.dll?ViewItem&item='.$reference.'&ssPageName=STRK:MESELX:IT&_trksid=p3984.m1555.l2649#ht_632wt_902';
        }
        return 'http://cgi'.($mode_dev ? '.sandbox' : '').'.ebay.'.$site_extension.'/ws/eBayISAPI.dll?ViewItem&item='.$reference.'&ssPageName=STRK:MESELX:IT&_trksid=p3984.m1555.l2649#ht_632wt_902';
    }

    public static function getProductsBlocked($id_ebay_profile)
    {
        $product_ids = Db::getInstance()->executeS('SELECT epc.`id_product`
			FROM `'._DB_PREFIX_.'ebay_product_configuration` epc
			 WHERE `blacklisted` = 1 and id_ebay_profile = '.$id_ebay_profile);

        return $product_ids;
    }

    public static function getCountOrphanListing($id_ebay_profile)
    {
        $ebay_profile = new EbayProfile((int) $id_ebay_profile);
        $is_one_five = version_compare(_PS_VERSION_, '1.5', '>') ? 1 : 0;
        if ($is_one_five) {
            // to check if a product has attributes (multi-variations),
            // we check if it has a "default_on" attribute in the product_attribute table
            $query = 'SELECT COUNT(DISTINCT(ep.`id_ebay_product`)) as `number`
    FROM `'._DB_PREFIX_.'ebay_product` ep
    
    
    
    LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
    ON epc.`id_product` = ep.`id_product`
    AND epc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'product` p
    ON p.`id_product` = ep.`id_product`
    '.Shop::addSqlAssociation('product', 'p').'


    LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
    ON pa.`id_product` = p.`id_product`
    AND pa.default_on = 1

    LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
    ON ecc.`id_category` = product_shop.`id_category_default`
    AND ecc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec
    ON ec.`id_ebay_category` = ecc.`id_ebay_category`

    WHERE ep.`id_ebay_profile` = '.(int)$ebay_profile->id.'
    AND ( p.`id_product` IS NULL OR ecc.`id_ebay_category_configuration` IS NULL OR  p.`active` != 1 OR epc.`blacklisted` != 0 OR ec.`id_category_ref` IS NULL OR ecc.`sync` = 0) ';
        } else {
            // to check if a product has attributes (multi-variations),
            // we check if it has a "default_on" attribute in the product_attribute table
            $query = 'SELECT COUNT(DISTINCT(ep.`id_ebay_product`)) as `number`
    FROM `'._DB_PREFIX_.'ebay_product` ep
    
    LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
    ON epc.`id_product` = ep.`id_product`
    AND epc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'product` p
    ON p.`id_product` = ep.`id_product`


    LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
    ON pa.`id_product` = p.`id_product`
    AND pa.default_on = 1

    LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
    ON ecc.`id_category` = p.`id_category_default`
    AND ecc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec
    ON ec.`id_ebay_category` = ecc.`id_ebay_category`

    WHERE ep.`id_ebay_profile` = '.(int)$ebay_profile->id.'
    AND ( p.`id_product` IS NULL OR ecc.`id_ebay_category_configuration` IS NULL OR  p.`active` != 1 OR epc.`blacklisted` != 0 OR ec.`id_category_ref` IS NULL OR ecc.`sync` = 0) ';
        }

        return  Db::getInstance()->executeS($query);
    }

    public static function getOrphanListing($id_ebay_profile, $page_current, $length = 20)
    {
        $offset = ((int) $page_current - 1) * (int) $length;
        $ebay_profile = new EbayProfile((int) $id_ebay_profile);
        $ebay_request = new EbayRequest();
        $is_one_five = version_compare(_PS_VERSION_, '1.5', '>') ? 1 : 0;
        if ($is_one_five) {
            // to check if a product has attributes (multi-variations),
            // we check if it has a "default_on" attribute in the product_attribute table
            $query = 'SELECT DISTINCT(ep.`id_ebay_product`),
        ep.`id_product_ref`,
        ep.`id_product`,
        ep.`id_attribute`                    AS `notSetWithMultiSkuCat`,
        epc.`blacklisted`,
        p.`id_product`                       AS `exists`,
        product_shop.`id_category_default`,
        p.`active`,
        pa.`id_product_attribute`            AS isMultiSku,
        pl.`name`                            AS psProductName,
        ecc.`id_ebay_category_configuration` AS EbayCategoryExists,
        ec.`is_multi_sku`                    AS EbayCategoryIsMultiSku,
        ecc.`sync`                           AS sync,
        ec.`id_category_ref`,
        etm.`id_task`
    FROM `'._DB_PREFIX_.'ebay_product` ep
    
    LEFT JOIN `'._DB_PREFIX_.'ebay_task_manager` etm 
    ON ep.`id_product` = etm.`id_product`
    
    LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
    ON epc.`id_product` = ep.`id_product`
    AND epc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'product` p
    ON p.`id_product` = ep.`id_product`
    '.Shop::addSqlAssociation('product', 'p').'

    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
    ON pl.`id_product` = p.`id_product`
    AND pl.`id_lang` = '.(int)$ebay_profile->id_lang.'

    LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
    ON pa.`id_product` = p.`id_product`
    AND pa.default_on = 1

    LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
    ON ecc.`id_category` = product_shop.`id_category_default`
    AND ecc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec
    ON ec.`id_ebay_category` = ecc.`id_ebay_category`

    WHERE ep.`id_ebay_profile` = '.(int)$ebay_profile->id.' ORDER BY ep.`id_ebay_profile` LIMIT '.$length.' OFFSET '.$offset;
        } else {
            // to check if a product has attributes (multi-variations),
            // we check if it has a "default_on" attribute in the product_attribute table
            $query = 'SELECT DISTINCT(ep.`id_ebay_product`),
        ep.`id_product_ref`,
        ep.`id_product`,
        ep.`id_attribute`                    AS `notSetWithMultiSkuCat`,
        epc.`blacklisted`,
        p.`id_product`                       AS `exists`,
        p.`id_category_default`,
        p.`active`,
        pa.`id_product_attribute`            AS isMultiSku,
        pl.`name`                            AS psProductName,
        ecc.`id_ebay_category_configuration` AS EbayCategoryExists,
        ec.`is_multi_sku`                    AS EbayCategoryIsMultiSku,
        ecc.`sync`                           AS sync,
        ec.`id_category_ref`
    FROM `'._DB_PREFIX_.'ebay_product` ep
    
    LEFT JOIN `'._DB_PREFIX_.'ebay_product_configuration` epc
    ON epc.`id_product` = ep.`id_product`
    AND epc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'product` p
    ON p.`id_product` = ep.`id_product`

    LEFT JOIN `'._DB_PREFIX_.'product_lang` pl
    ON pl.`id_product` = p.`id_product`
    AND pl.`id_lang` = '.(int)$ebay_profile->id_lang.'

    LEFT JOIN `'._DB_PREFIX_.'product_attribute` pa
    ON pa.`id_product` = p.`id_product`
    AND pa.default_on = 1

    LEFT JOIN `'._DB_PREFIX_.'ebay_category_configuration` ecc
    ON ecc.`id_category` = p.`id_category_default`
    AND ecc.`id_ebay_profile` = '.(int)$ebay_profile->id.'

    LEFT JOIN `'._DB_PREFIX_.'ebay_category` ec
    ON ec.`id_ebay_category` = ecc.`id_ebay_category`

    WHERE ep.`id_ebay_profile` = '.(int)$ebay_profile->id.' ORDER BY ep.`id_ebay_profile` LIMIT '.$length.' OFFSET '.$offset;
        }
//$currency = new Currency((int)$ebay_profile->getConfiguration('EBAY_CURRENCY'));
        $ebay = new Ebay();
// categories
        $category_list = $ebay->getChildCategories(Category::getCategories($ebay_profile->id_lang, false), $is_one_five);

// eBay categories

        $ebay_categories = EbayCategoryConfiguration::getEbayCategories($ebay_profile->id);

        $res = Db::getInstance()->executeS($query);

        $final_res = array();
        foreach ($res as &$row) {
            $ret =false;
            if (!$row['exists']) {
                $ret = true;
            } elseif (!$row['EbayCategoryExists']) {
                $ret = true;
            } elseif (!$row['active'] || $row['blacklisted']) {
                $ret = true;
            } elseif (is_null($row['id_category_ref'])) {
                $ret = true;
            } elseif (!$row['sync']) {
                $ret = true;
            }

            if ($ret === false) {
                continue;
            }

            if (isset($row['id_product_ref']) && $row['id_product_ref']) {
                $row['link'] = EbayProduct::getEbayUrl($row['id_product_ref'], $ebay_request->getDev());
            }

            if (isset($row['id_category_default']) && $row['id_category_default']) {
                foreach ($category_list as $cat) {
                    if (isset($cat['id_category']) && ($cat['id_category'] == $row['id_category_default'])) {
                        $row['category_full_name'] = $cat['name'];
                        break;
                    }
                }
            }

            if ($row['id_category_ref']) {
                foreach ($ebay_categories as $cat) {
                    if ($cat['id'] == $row['id_category_ref']) {
                        $row['ebay_category_full_name'] = $cat['name'];
                        break;
                    }
                }
                $ebayCategory = new EbayCategory($ebay_profile, $row['id_category_ref']);
                $row['EbayCategoryIsMultiSku'] = $ebayCategory->isMultiSku();
            }

            $final_res[] = $row;
        }

        return $final_res;
    }
}
