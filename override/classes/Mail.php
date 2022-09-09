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
class Mail extends MailCore
{
    public static function send(
        $idLang,
        $template,
        $subject,
        $templateVars,
        $to,
        $toName = null,
        $from = null,
        $fromName = null,
        $fileAttachment = null,
        $mode_smtp = null,
        $templatePath = _PS_MAIL_DIR_,
        $die = false,
        $idShop = null,
        $bcc = null,
        $replyTo = null,
        $replyToName = null
    ) {
        // don't send e-mails while creating the orders from ebay
        if (isset($templateVars['{order_name}']) &&
            Module::isEnabled('ebay') &&
            Configuration::get('EBAY_STATUS_ORDER')) {
            $id_ebay_status_order = (int) Configuration::get('EBAY_STATUS_ORDER');
            $order_ref = $templateVars['{order_name}'];
            $orderCollection = Order::getByReference($order_ref);
            $order = $orderCollection->getFirst();
            if (Validate::isLoadedObject($order) && $order->current_state == $id_ebay_status_order) {
                return true;
            }
        }

        return parent::send($idLang, $template, $subject, $templateVars, $to, $toName, $from, $fromName, $fileAttachment, $mode_smtp, $templatePath, $die, $idShop, $bcc, $replyTo, $replyToName);
    }
}
