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
class EbayFormTemplateManagerTab extends EbayTab
{
    public function getContent()
    {
        $iso = $this->context->language->iso_code;

        // Display Form
        $url_vars = [
            'id_tab' => '4',
            'section' => 'template',
        ];

        $url_vars['controller'] = Tools::getValue('controller');

        $action_url = $this->_getUrl($url_vars);

        if (Tools::getValue('reset_template')) {
            $ebay_product_template = EbayProductTemplate::getContent($this->ebay, $this->smarty);
        } else {
            $ebay_product_template = Tools::getValue('ebay_product_template', $this->ebay_profile->getConfiguration('EBAY_PRODUCT_TEMPLATE'));
        }

        $ebay_product_template_title = $this->ebay_profile->getConfiguration('EBAY_PRODUCT_TEMPLATE_TITLE');
        $this->context->controller->addCSS(
            _MODULE_DIR_ . 'ebay/views/css/ebay.css'
        );
        $smarty_vars = [
            'action_url' => $action_url,
            'ebay_product_template' => $ebay_product_template,
            'ebay_product_template_title' => $ebay_product_template_title,
            'features_product' => Feature::getFeatures($this->ebay_profile->id_lang),
            'ad' => dirname($_SERVER['PHP_SELF']),
            'base_uri' => __PS_BASE_URI__,
            'theme_css_dir' => _THEME_CSS_DIR_,
        ];

        $smarty_vars['iso'] = (file_exists(_PS_ROOT_DIR_ . '/js/tiny_mce/langs/' . $iso . '.js') ? $iso : 'en');

        return $this->display('formTemplateManager.tpl', $smarty_vars);
    }

    public function postProcess()
    {
        $ebay_product_template = Tools::getValue('ebay_product_template');
        $ebay_product_template_title = Tools::getValue('ebay_product_template_title');
        if (empty($ebay_product_template_title)) {
            $ebay_product_template_title = '{TITLE}';
        }

        // work around for the tinyMCE bug deleting the css line
        $css_line = '<link rel="stylesheet" type="text/css" href="' . $this->_getModuleUrl() . 'views/css/ebay.css" />';
        $ebay_product_template = $css_line . $ebay_product_template;

        // Saving new configurations
        if ($this->ebay_profile->setConfiguration('EBAY_PRODUCT_TEMPLATE', $ebay_product_template, true) && $this->ebay_profile->setConfiguration('EBAY_PRODUCT_TEMPLATE_TITLE', $ebay_product_template_title)) {
            $products = EbayProduct::getProductsWithoutBlacklisted($this->ebay_profile->id_lang, $this->ebay_profile->id, true);
            EbayTaskManager::deleteErrors($this->ebay_profile->id);
            while ($product_id = $products->fetch(PDO::FETCH_ASSOC)) {
                $product = new Product($product_id['id_product'], false, $this->ebay_profile->id_lang);
                EbayTaskManager::addTask('update', $product, null, $this->ebay_profile->id);
            }

            return $this->ebay->displayConfirmation($this->ebay->l('Settings updated', 'ebayformtemplatemanagertab'));
        } else {
            return $this->ebay->displayError($this->ebay->l('Settings failed', 'ebayformtemplatemanagertab'));
        }
    }
}
