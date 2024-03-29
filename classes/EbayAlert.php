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
class EbayAlert
{
    private $ebay_profile;
    private $ebay;
    private $errors = [];
    private $warnings = [];
    private $infos = [];

    private $alerts;

    public function __construct(Ebay $obj)
    {
        $this->ebay = $obj;
        $this->ebay_profile = $obj->ebay_profile;
    }

    public function getAlerts()
    {
        $this->reset();
        $this->checkUrlDomain();
        $this->checkCronTask();

        $this->build();

        return $this->alerts;
    }

    private function reset()
    {
        $this->errors = [];
        $this->warnings = [];
        $this->infos = [];
    }

    private function build()
    {
        $this->alerts = array_merge($this->errors, $this->warnings, $this->infos);
    }

    public function checkNumberPhoto()
    {
        $context = Context::getContext();
        if ($this->ebay_profile->getConfiguration('EBAY_PICTURE_PER_LISTING') > 0) {
            $link = new EbayCountrySpec();
            $link->getPictureUrl();

            return [
                'type' => 'warning',
                'message' => $this->ebay->l('You will send more than one image. This can have financial consequences. Please verify this link'),
                'link_warn' => $link->getPictureUrl(),
                'kb' => [
                    'errorcode' => 'PICTURES_NUMBER_ABOVE_ZERO',
                    'lang' => $context->language->iso_code,
                    'module_version' => $this->ebay->version,
                    'prestashop_version' => _PS_VERSION_,
                ],
            ];
        }

        if ($this->ebay_profile->getConfiguration('EBAY_PICTURE_PER_LISTING') >= 12) {
            return [
                'type' => 'error',
                'message' => $this->ebay->l('You can\'t send more than 12 pictures per product. Please configure in Advanced Settings'),
                'kb' => [
                    'errorcode' => 'PICTURES_NUMBER_ABOVE_TWELVE',
                    'lang' => $context->language->iso_code,
                    'module_version' => $this->ebay->version,
                    'prestashop_version' => _PS_VERSION_,
                ],
            ];
        }

        return false;
    }

    public function sendDailyMail()
    {
        //For the moment we do not send emails
        return true;
        $this->getAlerts();

        if (!$this->formatEmail()) {
            return;
        }

        $template_vars = ['{content}' => $this->formatEmail()];

        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'ebayAlert',
            Mail::l('Recap of your eBay module', (int) Configuration::get('PS_LANG_DEFAULT')),
            $template_vars,
            (string) Configuration::get('PS_SHOP_EMAIL'),
            null,
            (string) Configuration::get('PS_SHOP_EMAIL'),
            (string) Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            dirname(__FILE__) . '/../views/templates/mails/'
        );
        $this->reset();
    }

    public function formatEmail()
    {
        $templates_vars = [];

        $templates_vars['errors'] = (!empty($this->errors)) ? $this->errors : '';
        $templates_vars['warnings'] = (!empty($this->warnings)) ? $this->warnings : '';
        $templates_vars['infos'] = (!empty($this->infos)) ? $this->infos : '';

        if (empty($templates_vars)) {
            return false;
        }

        $smarty = Context::getContext()->smarty;
        $smarty->assign($templates_vars);

        return $this->ebay->display(realpath(dirname(__FILE__) . '/../'), '/views/templates/hook/alert_mail.tpl');
    }

    public function checkUrlDomain()
    {
        // check domain
        $shop = $this->ebay_profile instanceof EbayProfile ? new Shop($this->ebay_profile->id_shop) : new Shop();

        $wrong_domain = ($_SERVER['HTTP_HOST'] != $shop->domain && $_SERVER['HTTP_HOST'] != $shop->domain_ssl && Tools::getValue('ajax') == false);
        $domain = isset($shop->domain_ssl) ? $shop->domain_ssl : $shop->domain . DIRECTORY_SEPARATOR . $shop->physical_uri;

        if ($wrong_domain) {
            // if (version_compare(_PS_VERSION_, '1.5', '>'))
            //     $url_vars['controller'] = 'AdminMeta';
            // else
            //     $url_vars['tab'] = 'AdminMeta';
            // $warning_url = $this->_getUrl($url_vars);

            $context = Context::getContext();
            $protocol = Configuration::get('PS_SSL_ENABLED') ? 'https' : 'http';
            $this->errors[] = [
                'type' => 'error',
                'message' => $this->ebay->l('You are currently connected to the Prestashop Back Office using a different URL than set up, this module will not work properly. Please log in using @link@this url.@/link@'),
                'link_warn' => $protocol . '://' . $domain . DIRECTORY_SEPARATOR . basename(_PS_ADMIN_DIR_) . DIRECTORY_SEPARATOR,
                'kb' => [
                    'errorcode' => 'HELP-ALERT-DEFAULT-PS-URL',
                    'lang' => $context->language->iso_code,
                    'module_version' => $this->ebay->version,
                    'prestashop_version' => _PS_VERSION_,
                ],
            ];
        }
    }

    private function _getUrl($extra_vars = [])
    {
        $url_vars = [
            'configure' => Tools::getValue('configure'),
            'token' => Tools::getAdminTokenLite($extra_vars['controller']),
            'tab_module' => Tools::getValue('tab_module'),
            'module_name' => Tools::getValue('module_name'),
        ];

        return 'index.php?' . http_build_query(array_merge($url_vars, $extra_vars));
    }

    private function checkCronTask()
    {
        // PRODUCTS
        if ((int) Configuration::get('EBAY_SYNC_PRODUCTS_BY_CRON') == 1) {
            if ($last_sync_datetime = Configuration::get('DATE_LAST_SYNC_PRODUCTS')) {
                $warning_date = strtotime(date('Y-m-d') . ' - 2 days');

                $date = date('Y-m-d', strtotime($last_sync_datetime));
                $time = date('H:i:s', strtotime($last_sync_datetime));

                $msg = $this->ebay->l('Last product synchronization has been done the', get_class($this)) . ' ' . $date . ' ' . $this->ebay->l('at', get_class($this)) . ' ' . $time;

                if (strtotime($last_sync_datetime) < $warning_date) {
                    $this->warnings[] = [
                        'type' => 'warning',
                        'message' => $msg,
                    ];
                } else {
                    $this->infos[] = [
                        'type' => 'info',
                        'message' => $msg,
                    ];
                }
            } else {
                $this->errors[] = [
                    'type' => 'error',
                    'message' => $this->ebay->l('The product cron job has never been run.', get_class($this)),
                ];
            }
        }

        // ORDERS
        if ((int) Configuration::get('EBAY_SYNC_ORDERS_BY_CRON') == 1) {
            if ($this->ebay_profile->getConfiguration('EBAY_ORDER_LAST_UPDATE') != null) {
                $datetime = new DateTime($this->ebay_profile->getConfiguration('EBAY_ORDER_LAST_UPDATE'));

                $date = date('Y-m-d', strtotime($datetime->format('Y-m-d H:i:s')));
                $time = date('H:i:s', strtotime($datetime->format('Y-m-d H:i:s')));

                $datetime2 = new DateTime();

                $interval = round(($datetime2->format('U') - $datetime->format('U')) / (60 * 60 * 24));

                if ($interval >= 1) {
                    $this->errors[] = [
                        'type' => 'error',
                        'message' => $this->ebay->l('Last order synchronization has been done the ', get_class($this)) . $date . $this->ebay->l(' at ', get_class($this)) . $time,
                    ];
                }
            } else {
                $this->errors[] = [
                    'type' => 'error',
                    'message' => $this->ebay->l('Order cron job has never been run.', get_class($this)),
                ];
            }
        }

        // Returns
        if ((int) Configuration::get('EBAY_SYNC_ORDERS_RETURNS_BY_CRON') == 1) {
            if ($this->ebay_profile->getConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE') != null) {
                $datetime = new DateTime($this->ebay_profile->getConfiguration('EBAY_ORDER_RETURNS_LAST_UPDATE'));

                $date = date('Y-m-d', strtotime($datetime->format('Y-m-d H:i:s')));
                $time = date('H:i:s', strtotime($datetime->format('Y-m-d H:i:s')));

                $datetime2 = new DateTime();

                $interval = round(($datetime2->format('U') - $datetime->format('U')) / (60 * 60 * 24));

                if ($interval >= 1) {
                    $this->errors[] = [
                        'type' => 'error',
                        'message' => $this->ebay->l('Last order returns synchronization has been done the ', get_class($this)) . $date . $this->ebay->l('at ', get_class($this)) . $time,
                    ];
                }
            } else {
                $this->errors[] = [
                    'type' => 'error',
                    'message' => $this->ebay->l('Order return Cron Job has never been done', get_class($this)),
                ];
            }
        }
    }
}
