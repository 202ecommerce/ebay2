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

class AdminFormAdvancedParametersController extends ModuleAdminController
{
    public function init()
    {
        parent::init();
        if (Tools::getValue('action') == 'getLogs') {
            $this->getLogs();
        }
    }

    private function getLogs()
    {
        $url = dirname(__FILE__) . '/../../log/request.txt';
        $data = Tools::file_get_contents($url);
        $text = htmlentities($data);
        die('<pre>' . $text);
    }

    public function ajaxProcessClearLogs()
    {
        $url = dirname(__FILE__) . '/../../log/request.txt';
        if (file_exists($url) && Configuration::get('EBAY_ACTIVATE_LOGS')) {
            file_put_contents($url, "");
            echo json_encode("success");
        }
    }

    public function ajaxProcessCheckDatabase()
    {
        if (Module::isInstalled('ebay')) {
            $enable = Module::isEnabled('ebay');
            if ($enable) {
                $validator = new EbayDbValidator();

                if (Tools::getValue('actionProcess') == 'getNbTable') {
                    echo (int) $validator->getNbTable();
                } else if (Tools::getValue('actionProcess') == 'checkSpecific' && Tools::getValue('value')) {
                    if ($validator->checkSpecificTable((int) Tools::getValue('value'))) {
                        echo Tools::jsonEncode($validator->getLog());
                    } else {
                        echo Tools::jsonEncode(array('finish' => array(array('status' => 'stop', 'action' => 'End of checking'))));
                    }
                } else if (Tools::getValue('actionProcess') == 'checkAll') {
                    $validator->checkDatabase();
                    echo Tools::jsonEncode($validator->getLog());
                }
            }
            die();
        }
    }

    public function ajaxProcessCheckCategory()
    {
        if (Module::isInstalled('ebay')) {
            $enable = Module::isEnabled('ebay');
            if ($enable) {
                $validator = new EbayDbValidator();
                if (Tools::getValue('actionProcess') == 'checkCategories') {
                    $step = Tools::getValue('step');
                    $ebay_request = new EbayRequest();
                    if ($step == 1) {
                        $sql = 'CREATE TABLE IF NOT EXISTS `'._DB_PREFIX_.'ebay_category_tmp` (
                                id INT(11) NOT NULL AUTO_INCREMENT,
                                name VARCHAR(45) DEFAULT NULL,
                                id_categories int,
                                id_categories_ref_parent int,
                                level tinyint(1),
                                PRIMARY KEY (id)
                                )';
                        Db::getInstance()->execute($sql);
                        if ($cat_root = $ebay_request->getCategories(false)) {
                            die(Tools::jsonEncode($cat_root));
                        } else {
                            die(Tools::jsonEncode('error'));
                        }
                    } else if ($step == 2) {
                        ini_set('max_execution_time', 300);
                        $cat = Tools::getValue('id_categories');
                        $cats = $ebay_request->getCategories((int) $cat);
                        foreach ($cats as $cat) {
                            Db::getInstance()->insert('ebay_category_tmp', array(
                                'id_categories' => pSQL($cat['CategoryID']),
                                'name' => pSQL($cat['CategoryName']),
                                'id_categories_ref_parent' => pSQL($cat['CategoryParentID']),
                                'level' => pSQL($cat['CategoryLevel'])
                            ));
                            Db::getInstance()->execute('INSERT INTO `'._DB_PREFIX_.'ebay_category_tmp` (`name`,`id_categories`,`id_categories_ref_parent`,`level`) VALUES ("'.pSQL($cat['CategoryName']).'", 
                    '.pSQL($cat['CategoryID']).', 
                    '.pSQL($cat['CategoryParentID']).', 
                    '.pSQL($cat['CategoryLevel']).')');
                            if (isset($cat['BestOfferEnabled'])) {
                                EbayCategory::setBestOffer($cat['CategoryID'], $cat['BestOfferEnabled'], Tools::getValue('id_profile_ebay'));
                            }
                        };
                        die(Tools::jsonEncode($cat));
                    } elseif ($step == 3) {
                        $id_profile_ebay = Tools::getValue('id_profile_ebay');
                        ini_set('max_execution_time', 300);
                        $res=$validator->comparationCategories($id_profile_ebay);
                        $validator->deleteTmp();
                        echo Tools::jsonEncode($res);
                    }
                }
            }
            die();
        }
    }
}