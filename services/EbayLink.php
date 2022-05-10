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

namespace Ebay\services;

use Configuration;
use Context;
use Shop;
use Tools;
use Validate;

class EbayLink
{
    protected $context;

    public function __construct($context = null)
    {
        if ($context instanceof Context) {
            $this->context = $context;
        } else {
            $this->context = Context::getContext();
        }
    }

    public function getAdminLink($controller, $withToken = true, $sfRouteParams = [], $params = [])
    {
        if (version_compare(_PS_VERSION_, '1.7.0', '>=')) {
            return $this->context->link->getAdminLink(
                $controller,
                $withToken,
                $sfRouteParams,
                $params
            );
        }

        return $this->getAdminBaseLink() . basename(_PS_ADMIN_DIR_) . '/' . $this->context->link->getAdminLink($controller, $withToken);
    }

    /**
     * method Link::getAdminBaseLink() doesn't exist in PS 1.6
     */
    protected function getAdminBaseLink($idShop = null, $ssl = null, $relativeProtocol = false)
    {
        if (null === $ssl) {
            $ssl = Configuration::get('PS_SSL_ENABLED') && Configuration::get('PS_SSL_ENABLED_EVERYWHERE') && Tools::usingSecureMode();
        }

        $shop = Context::getContext()->shop;

        if (false == Validate::isLoadedObject($shop)) {
            $shop = new Shop(Configuration::get('PS_SHOP_DEFAULT'));
        }

        if ($relativeProtocol) {
            $base = '//' . ($ssl ? $shop->domain_ssl : $shop->domain);
        } else {
            $base = (($ssl) ? 'https://' . $shop->domain_ssl : 'http://' . $shop->domain);
        }

        return $base . $shop->getBaseURI();
    }
}
