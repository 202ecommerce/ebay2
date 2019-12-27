<?php

class Mail extends MailCore
{
    public static function Send(
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
        $replyToName = null)
    {
        // don't send e-mails while creating the orders from ebay
        if (isset($templateVars['{order_name}']) &&
            Module::isEnabled('ebay') &&
            Configuration::get('EBAY_STATUS_ORDER'))
        {
            $id_ebay_status_order = (int) Configuration::get('EBAY_STATUS_ORDER');
            $order_ref = $templateVars['{order_name}'];
            $orderCollection = Order::getByReference($order_ref);
            $order = $orderCollection->getFirst();
            if (Validate::isLoadedObject($order) && $order->current_state == $id_ebay_status_order) {
                return true;
            }
        }
        return parent::Send($idLang, $template, $subject, $templateVars, $to, $toName, $from, $fromName, $fileAttachment, $mode_smtp, $templatePath, $die, $idShop, $bcc, $replyTo, $replyToName);
    }
}