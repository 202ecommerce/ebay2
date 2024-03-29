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
class EbayTab
{
    /** @var Ebay */
    protected $ebay;
    /** @var Smarty_Data */
    protected $smarty;
    /** @var EbayProfile */
    protected $ebay_profile;
    /** @var Context */
    protected $context;
    protected $path;

    public function __construct($ebay, $smarty, $context = null, $path = null)
    {
        $this->ebay = $ebay;
        $this->ebay_profile = $ebay->ebay_profile;
        $this->smarty = $smarty;
        $this->context = $context;
        $this->path = $path;
    }

    protected function display($template, $template_vars)
    {
        $this->smarty->assign($template_vars);

        return $this->ebay->display(dirname(__FILE__) . '/../../ebay.php', '/views/templates/hook/' . $template);
    }

    protected function _getUrl($extra_vars = [])
    {
        $url_vars = [
            'configure' => Tools::getValue('configure'),
            'token' => Tools::getValue('token'),
            'tab_module' => Tools::getValue('tab_module'),
            'module_name' => Tools::getValue('module_name'),
        ];

        return 'index.php?' . http_build_query(array_merge($url_vars, $extra_vars));
    }

    /**
     * Returns the module url
     *
     **/
    protected function _getModuleUrl()
    {
        return Tools::getShopDomainSsl(true) . __PS_BASE_URI__ . 'modules/ebay/';
    }

    protected function initLinkService()
    {
        return new \Ebay\services\EbayLink();
    }
}
