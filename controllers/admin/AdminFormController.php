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
class AdminFormController extends ModuleAdminController
{
    public function ajaxProcessLoadNbTasksInWork()
    {
        $id_profile = Tools::getValue('id_profile');
        if ($id_profile) {
            $table = _DB_PREFIX_ . 'ebay_task_manager';
            $sql_select = 'SELECT COUNT(DISTINCT(id_product)) AS nb  FROM `' . pSQL($table) . '` WHERE `locked` != 0 AND `id_ebay_profile` = ' . (int) $id_profile;
            $res_select = Db::getInstance()->executeS($sql_select);
            $nb_tasks_in_work = $res_select[0]['nb'];
            exit($nb_tasks_in_work);
        }
        exit('0');
    }

    public function ajaxProcessBoostMode()
    {
        Tools::file_get_contents(Tools::getValue('cron_url'));
        $nb_tasks = Db::getInstance()->executeS('SELECT COUNT(*) AS nb	FROM ' . _DB_PREFIX_ . 'ebay_task_manager WHERE (`error_code` = \'0\' OR `error_code` IS NULL)');
        echo $nb_tasks[0]['nb'];
        exit();
    }

    public function ajaxProcessDeleteProfile()
    {
        if (Module::isInstalled('ebay')) {
            $enable = Module::isEnabled('ebay');
            if ($enable) {
                exit(EbayProfile::deleteById((int) Tools::getValue('profile')));
            }
        }
    }

    public function ajaxProcessCheckToken()
    {
        $result = EbayConfiguration::updateAPIToken() ? 'OK' : 'KO';
        exit($result);
    }

    public function ajaxProcessGetCountriesLocation()
    {
        $id_ebay_profile = (int) Tools::getValue('profile');
        $sql = 'SELECT * FROM ' . _DB_PREFIX_ . 'ebay_shipping_zone_excluded
                WHERE `id_ebay_profile` = ' . (int) $id_ebay_profile . '
                AND region = \'' . pSQL(Tools::getValue('region')) . '\'';
        $countries = Db::getInstance()->ExecuteS($sql);

        if (count($countries)) {
            $string = '';
            $context = Context::getContext();
            $ebay = new Ebay();
            foreach ($countries as $country) {
                $tpl_var = [
                    'location' => Tools::safeOutput($country['location']),
                    'country' => $country,
                ];
                $context->smarty->assign($tpl_var);
                $string .= $ebay->display(realpath(dirname(__FILE__) . '/../../'), '/views/templates/hook/form_countriesShipping.tpl');
            }

            echo $string;
            exit();
        } else {
            echo 'No countries were found for this region';
            exit();
        }
    }

    public function ajaxProcessPreviewTemplate()
    {
        $ebay = Module::getInstanceByName('ebay');
        $ebay->ajaxPreviewTemplate(Tools::getValue('message'), (int) Tools::getValue('id_lang'));
    }

    public function ajaxProcessLoadCategoriesFromEbay()
    {
        if (!($id_profile = Tools::getValue('profile')) || !Validate::isInt($id_profile)
            || !($step = Tools::getValue('step')) || !Validate::isInt($step)
            || !($cat = Tools::getValue('id_category'))
        ) {
            exit('ERROR : INVALID DATA');
        }

        if (Module::isInstalled('ebay')) {
            $enable = Module::isEnabled('ebay');

            if ($enable) {
                $ebay_request = new EbayRequest();
                $ebay_profile = new EbayProfile((int) $id_profile);

                if ($step == 1) {
                    EbayCategory::deleteCategoriesByIdCountry($ebay_profile->ebay_site_id);
                    $ebay_profile->setCatalogConfiguration('EBAY_CATEGORY_LOADED', 0);
                    if ($cat_root = $ebay_request->getCategories(false)) {
                        exit(Tools::jsonEncode($cat_root));
                    } else {
                        exit(Tools::jsonEncode('error'));
                    }
                } elseif ($step == 2) {
                    $cat = $ebay_request->getCategories((int) $cat);
                    if (empty($cat) || EbayCategory::insertCategories($ebay_profile->ebay_site_id, $cat, $ebay_request->getCategoriesSkuCompliancy())) {
                        exit(Tools::jsonEncode($cat));
                    } else {
                        exit(Tools::jsonEncode('error'));
                    }
                } elseif ($step == 3) {
                    //Configuration::updateValue('EBAY_CATEGORY_LOADED_'.$ebay_profile->ebay_site_id, 1);
                    $ebay_profile->setCatalogConfiguration('EBAY_CATEGORY_LOADED', 1);
                }
            }
        }
    }

    public function ajaxProcessCheckTls()
    {
        $isTlsValid = $this->getTlsValidator()->isValid();
        $message = $this->l('Your host is compatible with TLS 1.2:');

        if ($isTlsValid) {
            $message .= $this->l('Yes');
        } else {
            $message .= $this->l('No');
        }

        $response = [
            'message' => $message,
        ];
        exit(json_encode($response));
    }

    /**
     * @return TlsValidator
     */
    protected function getTlsValidator()
    {
        return new TlsValidator();
    }
}
