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

if (file_exists(dirname(__FILE__) . '/EbayRequest.php')) {
    require_once dirname(__FILE__) . '/EbayRequest.php';
}

class EbayConfiguration
{
    /**
     * Updates Ebay API Token and stores it
     *
     * Returns true is sucessful, false otherwise
     *
     * @return bool
     */
    public static function updateAPIToken()
    {
        $context = Context::getContext();
        $profile_data = explode('_', $context->cookie->ebayCurrentProfile);

        $request = new EbayRequest($profile_data[0]);
        $ebay_profile = EbayProfile::getCurrent(false);

//        if ($token = $request->fetchToken(Configuration::get('EBAY_API_USERNAME', null, 0, 0), Configuration::get('EBAY_API_SESSION', null, 0, 0)))
        if ($token = $request->fetchToken($ebay_profile->ebay_user_identifier, Configuration::get('EBAY_API_SESSION', null, 0, 0))) {
            $ebay_profile->setToken($token);
            $request->importBusinessPolicies();
            //Configuration::updateValue('EBAY_API_TOKEN', $token, false, 0, 0);
            Configuration::updateValue('EBAY_TOKEN_REGENERATE', false, false, 0, 0);
            $errors = EbayTaskManager::getTaskByErrorCode('931', $ebay_profile->id);
            if (!empty($errors)) {
                Db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE `error_code` = "931" AND `id_ebay_profile` = ' . (int) $ebay_profile->id);
                foreach ($errors as $error) {
                    EbayTaskManager::insertTask($error['id_product'], $error['id_product_attribute'], $error['id_task'], $error['id_ebay_profile']);
                }
            }

            return true;
        }

        return false;
    }

    public static function get($id_ebay_profile, $name)
    {
        return Db::getInstance()->getValue('SELECT `value`
			FROM `' . _DB_PREFIX_ . 'ebay_configuration`
			WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
			AND `name` = "' . pSQL($name) . '" ORDER BY id_configuration DESC');
    }

    public static function set($id_ebay_profile, $name, $value, $html = false)
    {
        $name = pSQL($name);
        $checkExist = DB::getInstance()->ExecuteS('SELECT * FROM ' . _DB_PREFIX_ . "ebay_configuration WHERE id_ebay_profile=$id_ebay_profile AND `name`='$name'");
        if ($checkExist) {
            $datas = [
                'value' => pSQL($value, $html),
            ];

            return DB::getInstance()->update('ebay_configuration', $datas, " id_ebay_profile=$id_ebay_profile AND `name`='$name'");
        }
        $datas = [
            'id_ebay_profile' => (int) $id_ebay_profile,
            'name' => pSQL($name),
            'value' => pSQL($value, $html),
        ];
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        return $dbEbay->autoExecute(_DB_PREFIX_ . 'ebay_configuration', $datas, 'INSERT');
    }

    public static function getAll($id_ebay_profile, $exceptions = false)
    {
        $sql = 'SELECT `name`, `value`
			FROM `' . _DB_PREFIX_ . 'ebay_configuration`
			WHERE `id_ebay_profile` = "' . (int) $id_ebay_profile . '" AND name NOT IN ("' . implode('","', array_map('pSQL', $exceptions)) . '")';

        return Db::getInstance()->executeS($sql);
    }

    /**
     * For upgrade: takes the values in PS Configurations and stores them in Ebay Configurations
     *
     * Returns true is sucessful, false otherwise
     *
     * @param $id_ebay_profile
     * @param $attributes
     * @param $attributes_html
     *
     * @return bool
     */
    public static function PSConfigurationsToEbayConfigurations($id_ebay_profile, $attributes, $attributes_html)
    {
        foreach ($attributes as $name) {
            $ps_value = Configuration::get($name);
            $ebay_value = EbayConfiguration::get($id_ebay_profile, $name);
            if ($ps_value && !$ebay_value) {
                EbayConfiguration::set($id_ebay_profile, $name, $ps_value);
            }
        }
        foreach ($attributes_html as $name) {
            $ps_value = Configuration::get($name);
            $ebay_value = EbayConfiguration::get($id_ebay_profile, $name);
            if ($ps_value && !$ebay_value) {
                EbayConfiguration::set($id_ebay_profile, $name, $ps_value, true);
            }
        }
    }
}
