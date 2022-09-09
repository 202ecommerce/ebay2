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
class EbayOrder
{
    private $id_ebay_order;
    private $id_order_ref;
    private $amount;
    private $status;
    private $date;
    private $name;
    private $firstname;
    private $familyname;
    private $address1;
    private $address2;
    private $city;
    private $state;
    private $country_iso_code;
    private $country_name;
    private $phone;
    private $postalcode;
    public $shippingService;
    private $shippingServiceCost;
    private $email;
    private $product_list;
    private $payment_method;
    private $id_order_seller;
    private $date_add;
    private $id_currency;
    public $id_transaction;
    private $CODCost;
    private $ebaySiteName;
    public $ebay_user_identifier;

    private $error_messages = [];

    private $write_logs;

    /* PS variables */
    private $id_customers;
    private $id_address;
    private $id_orders;
    /** @var Cart[] */
    private $carts;
    /** @var string */
    protected $ioss;

    /** @var int */
    protected $idEbayProfile;

    public function __construct(SimpleXMLElement $order_xml = null)
    {
        if (!$order_xml) {
            return;
        }

        /** Backward compatibility */
        require dirname(__FILE__) . '/../backward_compatibility/backward.php';

        list($this->firstname, $this->familyname) = $this->_formatShippingAddressName($order_xml->ShippingAddress->Name);
        $this->id_order_ref = (string) $order_xml->OrderID;
        $this->amount = (string) $order_xml->AmountPaid;
        $this->status = (string) $order_xml->CheckoutStatus->Status;
        $this->name = (string) $order_xml->ShippingAddress->Name;
        $this->address1 = (string) $order_xml->ShippingAddress->Street1;
        $this->address2 = (string) $order_xml->ShippingAddress->Street2;
        $this->city = (string) $order_xml->ShippingAddress->CityName;
        $this->state = (string) $order_xml->ShippingAddress->StateOrProvince;
        $this->country_iso_code = (string) $order_xml->ShippingAddress->Country;
        $this->country_name = (string) $order_xml->ShippingAddress->CountryName;
        $this->postalcode = (string) $order_xml->ShippingAddress->PostalCode;
        $this->shippingService = (string) $order_xml->ShippingServiceSelected->ShippingService;
        $this->shippingServiceCost = (string) $order_xml->ShippingServiceSelected->ShippingServiceCost;
        $this->payment_method = (string) $order_xml->CheckoutStatus->PaymentMethod;
        $this->id_order_seller = (string) $order_xml->ShippingDetails->SellingManagerSalesRecordNumber;
        $this->ebaySiteName = $order_xml->TransactionArray->Transaction->Item->Site;
        $this->ebay_user_identifier = isset($order_xml->MonetaryDetails->Payments->Payment->Payee) ? $order_xml->MonetaryDetails->Payments->Payment->Payee : false;
        $this->ioss = $this->getIOSSFromResponse($order_xml);

        $amount_paid_attr = $order_xml->AmountPaid->attributes();
        $this->id_currency = Currency::getIdByIsoCode($amount_paid_attr['currencyID']);

        if ($order_xml->CheckoutStatus->PaymentMethod == 'COD' && isset($order_xml->ShippingDetails->CODCost)) {
            $this->shippingServiceCost += $order_xml->ShippingDetails->CODCost;
            $this->CODCost = (float) $order_xml->ShippingDetails->CODCost;
        }

        if (count($order_xml->TransactionArray->Transaction)) {
            $this->email = (string) $order_xml->TransactionArray->Transaction[0]->Buyer->Email;
            $this->id_transaction = (string) $order_xml->TransactionArray->Transaction->TransactionID;
        }

        $phone = (string) $order_xml->ShippingAddress->Phone;

        if (!$phone || !Validate::isPhoneNumber($phone)) {
            $this->phone = '0100000000';
        } else {
            $this->phone = $phone;
        }

        $this->id_orders = [];

        $date = Tools::substr((string) $order_xml->CreatedTime, 0, 10) . ' ' . Tools::substr((string) $order_xml->CreatedTime, 11, 8);
        $this->date = $date;
        $this->date_add = $date;

        if ($order_xml->TransactionArray->Transaction) {
            $this->product_list = $this->_getProductsFromTransactions($order_xml->TransactionArray);
        }

        $this->idEbayProfile = $this->getProfileByItemId($order_xml);

        if ($this->idEbayProfile == 0) {
            $this->idEbayProfile = $this->getProfileBySku($order_xml);
        }

        $this->write_logs = (bool) Configuration::get('EBAY_ACTIVATE_LOGS');
    }

    public function getEbaySiteName()
    {
        return $this->ebaySiteName;
    }

    /**
     * Define if order is complete
     * with status[Complete], amount > 0.1, and contain products
     *
     * @return bool
     */
    public function isCompleted()
    {
        return $this->status == 'Complete'
        && $this->amount > 0.1
        && !empty($this->product_list);
    }

    public function checkStatus()
    {
        return $this->status == 'Complete';
    }

    /**
     * Define if order has a country active in Prestashop configuration.
     *
     * @return bool
     */
    public function isCountryEnable()
    {
        if (!Tools::isEmpty($this->country_iso_code)) {
            $country = new Country(Country::getByIso($this->country_iso_code), (int) Configuration::get('PS_LANG_DEFAULT'));

            if ($country->active) {
                return true;
            } else {
                return false;
            }
        }

        return false;
    }

    /**
     * Define if order already exist with ps_ebay_order.id_order_ref
     *
     * @return bool
     */
    public function exists()
    {
        $order_in_ebay_order_table = (bool) Db::getInstance()->getValue('SELECT `id_ebay_order`
			FROM `' . _DB_PREFIX_ . 'ebay_order`
			WHERE `id_order_ref` = "' . pSQL($this->id_order_ref) . '"');

        return $order_in_ebay_order_table;
    }

    /**
     * Define if order has a valid email, firstname, and familyname
     *
     * @return bool
     */
    public function hasValidContact()
    {
        $this->familyname = empty($this->familyname) ? 'Undefined' : $this->familyname;
        $this->firstname = empty($this->firstname) ? 'Undefined' : $this->firstname;

        return Validate::isEmail($this->email)
        && $this->firstname
        && $this->familyname;
    }

    /**
     * @param EbayProfile $ebay_profile
     * @param EbayCountrySpec $ebayCountry
     *
     * @return int
     */
    public function getOrAddCustomer($ebay_profile, $ebayCountry)
    {
        $id_customer = (int) Db::getInstance()->getValue('SELECT `id_customer`
			FROM `' . _DB_PREFIX_ . 'customer`
			WHERE `active` = 1
			AND `email` = \'' . pSQL($this->email) . '\'
			AND `id_shop` = ' . (int) $ebay_profile->id_shop . '
			AND `deleted` = 0' . (Tools::substr(_PS_VERSION_, 0, 3) == '1.3' ? '' : ' AND `is_guest` = 0'));

        $format = new TotFormat();

        // Add customer if he doesn't exist
        //if ($id_customer < 1) RAPH
        if (!$id_customer) {
            $customer = new Customer();
            $customer->id_gender = 0;
            $customer->id_default_group = 1;
            $customer->secure_key = md5(uniqid(rand(), true));
            $customer->email = $format->formatEmail($this->email);
            $customer->passwd = md5(_COOKIE_KEY_ . rand());
            $customer->last_passwd_gen = date('Y-m-d H:i:s');
            $customer->newsletter = 0;
            if (empty($this->familyname)) {
                $this->familyname = 'no-send';
            }
            $customer->lastname = empty($this->familyname) ? 'Undefined' : $format->formatName(EbayOrder::_formatFamilyName($this->familyname));
            $customer->firstname = empty($this->firstname) ? 'Undefined' : $format->formatName($this->firstname);
            $customer->active = 1;
            $customer->optin = 0;
            $customer->id_shop = (int) $ebay_profile->id_shop;
            $customer->id_lang = $ebayCountry->getIdLang();
            $res = $customer->add();

            $this->_writeLog($ebay_profile->id, 'add_customer', $res);

            $id_customer = $customer->id;
        }

        $this->id_customers[$ebay_profile->id_shop] = $id_customer;

        return $id_customer;
    }

    public function updateOrAddAddress($ebay_profile)
    {
        $customer = new Customer((int) $this->id_customers[$ebay_profile->id_shop]);
        $addresses = $customer->getAddresses($this->context->language->id);
        $format = new TotFormat();
        $tempAddress = new Address();
        $address_exist = false;
        $id_address = 0;
        $count = 1;

        $tempAddress->id_customer = $customer->id;
        $tempAddress->id_country = (int) Country::getByIso($this->country_iso_code);
        $tempAddress->lastname = empty($format->formatName(EbayOrder::_formatFamilyName($this->familyname))) ? 'Undefined' : $format->formatName(EbayOrder::_formatFamilyName($this->familyname));
        $tempAddress->firstname = empty($format->formatName($this->firstname)) ? 'Undefined' : $format->formatName($this->firstname);
        $tempAddress->address1 = $format->formatAddress($this->address1);
        $tempAddress->address2 = $format->formatAddress($this->address2);
        $tempAddress->postcode = $format->formatPostCode(str_replace('.', '', $this->postalcode));
        $tempAddress->city = $format->formatCityName(empty($this->city) ? 'Undefined' : $this->city);

        if ($id_state = (int) State::getIdByIso(Tools::strtoupper($this->state), $tempAddress->id_country)) {
            $tempAddress->id_state = $id_state;
        } elseif ($id_state = State::getIdByName(pSQL(trim($this->state)))) {
            $state = new State((int) $id_state);
            if ($state->id_country == $tempAddress->id_country) {
                $tempAddress->id_state = $state->id;
            }
        }

        if (Country::isNeedDniByCountryId($tempAddress->id_country)) {
            $tempAddress->dni = 'ThereIsNotDni000';
        }

        if (!empty($this->phone)) {
            $phone = $format->formatPhoneNumber($this->phone);
            $phone_mobile = $format->formatPhoneNumber($this->phone);

            if (Validate::isPhoneNumber($phone)) {
                $tempAddress->phone = $format->formatPhoneNumber($this->phone);
            }

            if (Validate::isPhoneNumber($phone_mobile)) {
                $tempAddress->phone_mobile = $format->formatPhoneNumber($this->phone);
            }
        }

        $tempAddress->active = 1;

        foreach ($addresses as $address) {
            if ($address['lastname'] == $tempAddress->lastname
                    && $address['firstname'] == $tempAddress->firstname
                    && $address['address1'] == $tempAddress->address1
                    && $address['address2'] == $tempAddress->address2
                    && $address['id_country'] == $tempAddress->id_country
                    && $address['city'] == $tempAddress->city
                    && $address['id_state'] == $tempAddress->id_state
                    && $address['postcode'] == $tempAddress->postcode
                    && $address['phone'] == $tempAddress->phone
                    && $address['phone_mobile'] == $tempAddress->phone_mobile
                    && $address['dni'] == $tempAddress->dni
            ) {
                $address_exist = true;
                $id_address = $address['id_address'];
                break;
            } else {
                if ((strrpos($address['alias'], 'eBay')) !== false) {
                    ++$count;
                }
            }
        }

        if ($address_exist == false) {
            $tempAddress->alias = 'eBay ' . $count;
            $tempAddress->save();
            $id_address = $tempAddress->id;
        }

        $this->id_address = $id_address;

        return $id_address;
    }

    /**
     * Formats the family name to match eBay constraints:
     * - length < 32 chars
     * - no brackets ()
     *
     * @param $family_name
     *
     * @return mixed
     */
    private function _formatFamilyName($family_name)
    {
        return str_replace(['(', ')'], '', Tools::substr(pSQL($family_name), 0, 32));
    }

    public function getProductIds()
    {
        return array_map(
            function ($product) {
                return (int) $product['id_product'];
            },
            $this->product_list
        );
    }

    /**
     * Get eBay profile and Products and attribute by shop
     *
     * @return array All product in an array like
     *               $res[$id_shop]
     *               ['id_ebay_profiles']
     *               [0]
     *               [1]
     *               ['id_products']
     *               [0]
     *               ['id_product']
     *               ['id_product_attribute']
     *               [1]
     *               ['id_product']
     *               ['id_product_attribute']
     */
    public function getProductsAndProfileByShop()
    {
        $res = [];
        foreach ($this->product_list as $product) {
            if ($this->getIdEbayProfile()) {
                $ebay_profile = new EbayProfile($this->getIdEbayProfile());
            } else {
                $sql = 'SELECT epr.`id_ebay_profile`
				FROM `' . _DB_PREFIX_ . 'ebay_product` epr
				WHERE epr.`id_product` = ' . (int) $product['id_product'];
                $id_ebay_profile = (int) Db::getInstance()->getValue($sql);
                if ($id_ebay_profile) {
                    $ebay_profile = new EbayProfile($id_ebay_profile);
                } else {
                    // case where the row in ebay_product has disappeared like if the product has been removed from eBay before sync
                    if (Shop::isFeatureActive()) {
                        $sql = 'SELECT `id_ebay_profile`
							FROM `' . _DB_PREFIX_ . 'ebay_profile` ep
							LEFT JOIN `' . _DB_PREFIX_ . 'product_shop` ps
							ON ep.`id_shop` = ps.`id_shop`
							AND ps.`id_product` = ' . (int) $product['id_product'];
                        $id_ebay_profile = (int) Db::getInstance()->getValue($sql);
                        $ebay_profile = new EbayProfile($id_ebay_profile);
                    } else {
                        $ebay_profile = EbayProfile::getCurrent();
                    }
                }
            }

            if (!array_key_exists($ebay_profile->id_shop, $res)) {
                $res[$ebay_profile->id_shop] = [
                    'id_ebay_profiles' => [$ebay_profile->id],
                    'id_products' => [],
                ];
            } elseif (!in_array($ebay_profile->id, $res[$ebay_profile->id_shop]['id_ebay_profiles'])) {
                $res[$ebay_profile->id_shop]['id_ebay_profiles'][] = $ebay_profile->id;
            }

            // $res[$ebay_profile->id_shop]['id_products'][] = $product['id_product'];
            $res[$ebay_profile->id_shop]['id_products'][] = [
                'id_product' => $product['id_product'],
                'id_product_attribute' => $product['id_product_attribute'],
            ];
        }

        return $res;
    }

    /**
     * Define if product and attribute exist in Prestashop.
     *
     * @return bool
     */
    public function hasAllProductsWithAttributes()
    {
        foreach ($this->product_list as $product) {
            if ((int) $product['id_product'] < 1
                || !Db::getInstance()->getValue('SELECT `id_product`
					FROM `' . _DB_PREFIX_ . 'product`
					WHERE `id_product` = ' . (int) $product['id_product'])
            ) {
                return false;
            }

            if (isset($product['id_product_attribute'])
                && $product['id_product_attribute'] > 0
                && !Db::getInstance()->getValue('SELECT `id_product_attribute`
					FROM `' . _DB_PREFIX_ . 'product_attribute`
					WHERE `id_product` = ' . (int) $product['id_product'] . '
					AND `id_product_attribute` = ' . (int) $product['id_product_attribute'])
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add a new Cart with order content, and save it in database
     *
     * @param EbayProfile $ebay_profile
     * @param EbayCountrySpec $ebay_country
     *
     * @return Cart
     */
    public function addCart($ebay_profile, $ebay_country)
    {
        $id_carrier = (int) EbayShipping::getPsCarrierByEbayCarrier($ebay_profile->id, $this->shippingService);
        $cart = new Cart();

        $this->context->customer = new Customer($this->id_customers[$ebay_profile->id_shop]);

        $cart->id_shop = $ebay_profile->id_shop;
        $cart->id_customer = $this->id_customers[$ebay_profile->id_shop];
        $cart->id_address_invoice = $this->id_address;
        $cart->id_address_delivery = $this->id_address;
        $cart->id_carrier = $id_carrier;
        $cart->delivery_option = @serialize([$this->id_address => $id_carrier . ',']);
        $cart->id_lang = $ebay_country->getIdLang();
        //$cart->id_currency = Currency::getIdByIsoCode($ebay_country->getCurrency());
        $cart->id_currency = $this->id_currency;
        $cart->recyclable = 0;
        $cart->gift = 0;
        $res = $cart->add();

        $this->_writeLog($ebay_profile->id, 'add_cart', $res, null);

        $this->carts[$ebay_profile->id_shop] = $cart;

        return $cart;
    }

    public function deleteCart($id_shop)
    {
        return $this->carts[$id_shop]->delete();
    }

    /**
     * @param EbayProfile $ebay_profile
     *
     * @return bool true is still products in the cart, false otherwise
     */
    public function updateCartQuantities($ebay_profile)
    {
        $id_lang = (int) Configuration::get('PS_LANG_DEFAULT');
        $cart_nb_products = 0;

        $products_by_shop = $this->getProductsAndProfileByShop();

        if (isset($products_by_shop[$ebay_profile->id_shop])) {
            $product_list = $products_by_shop[$ebay_profile->id_shop]['id_products'];
        } else {
            $product_list = [];
        }

        // foreach ($product_list as $id_product)
        // {
        //     foreach($this->product_list as $product)
        //         if ($id_product == $product['id_product'])
        //             break;
        foreach ($product_list as $p) {
            foreach ($this->product_list as $product) {
                if ($p['id_product'] == $product['id_product'] && $p['id_product_attribute'] == $product['id_product_attribute']) {
                    break;
                }
            }

            // check if product is in this cart
            //            if (!count($this->carts[$ebay_profile->id_shop]->getProducts(false, $product['id_product'])))
            //                continue;

            $prod = new Product($product['id_product'], false, $id_lang);
            $minimal_quantity = empty($product['id_product_attribute']) ? $prod->minimal_quantity : (int) Attribute::getAttributeMinimalQty($product['id_product_attribute']);

            if ($product['quantity'] >= $minimal_quantity) {
                $id_product_attribute = empty($product['id_product_attribute']) ? null : $product['id_product_attribute'];
                $update = $this->carts[$ebay_profile->id_shop]->updateQty(
                    (int) $product['quantity'],
                    (int) $product['id_product'],
                    $id_product_attribute,
                    false,
                    'up',
                    0,
                    new Shop($ebay_profile->id_shop),
                    false
                );
                if ($update === true) {
                    ++$cart_nb_products;
                }
            } else {
                // minimal quantity for purchase not met
                $this->_sendMinimalQtyAlertEmail($prod->name, $minimal_quantity, $product['quantity']);
            }
        }

        $this->carts[$ebay_profile->id_shop]->update();

        $this->carts[$ebay_profile->id_shop]->getProducts(true);
        $this->carts[$ebay_profile->id_shop]->getDeliveryOptionList(null, true);

        return (bool) $cart_nb_products;
    }

    /**
     * Validate an order in database
     *
     * @param int $id_shop
     * @param int|null $id_ebay_profile
     *
     * @return int Current order's id
     *
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function validate($id_shop, $id_ebay_profile = null)
    {
        $customer = new Customer($this->id_customers[$id_shop]);
        $payment = new EbayPayment();

        //Change context's currency
        $this->context->currency = new Currency($this->carts[$id_shop]->id_currency);
        if (Configuration::get('PS_TAX_ADDRESS_TYPE') == 'id_address_delivery') {
            $this->context->country = new Country((int) Country::getByIso($this->country_iso_code));
        }

        try {
            $payment->validateOrder(
                (int) $this->carts[$id_shop]->id,
                (int) Configuration::get('EBAY_STATUS_ORDER'),
                (float) $this->carts[$id_shop]->getOrderTotal(true, Cart::BOTH),
                $this->getPaymentMethod(),
                null,
                [],
                (int) $this->carts[$id_shop]->id_currency,
                false,
                $customer->secure_key,
                new Shop((int) $id_shop)
            );
        } catch (Exception $e) {
            Ebay::debug($e->getMessage());
            $this->_writeLog($id_ebay_profile, $e->getMessage(), false, 'End of validate order FAIL!');
        }

        if (!$payment->currentOrder) {
            $id_cart = $this->carts[$id_shop]->id;
            $payment->currentOrder = (int) Order::getIdByCartId((int) $id_cart);
        }

        $this->id_orders[$id_shop] = $payment->currentOrder;

        $this->_writeLog($id_ebay_profile, 'validate_order', true, 'End of validate order');

        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        // Fix on date
        $dbEbay->autoExecute(_DB_PREFIX_ . 'orders', ['date_add' => pSQL($this->date_add)], 'UPDATE', '`id_order` = ' . (int) $this->id_orders[$id_shop]);

        return $payment->currentOrder;
    }

    /**
     * Update price of the current Order
     *
     * @param EbayProfile $ebay_profile
     *
     * @return bool
     *
     * @throws PrestaShopDatabaseException
     */
    public function updatePrice($ebay_profile)
    {
        $total_price_tax_excl = 0;
        $total_shipping_tax_incl = 0;
        $total_shipping_tax_excl = 0;
        $id_carrier = (int) EbayShipping::getPsCarrierByEbayCarrier($ebay_profile->id, $this->shippingService);

        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        $carrier_tax_rate = (float) Tax::getCarrierTaxRate((int) $id_carrier);

        foreach ($this->product_list as $product) {
            // check if product is in this cart
            if (!count($this->carts[$ebay_profile->id_shop]->getProducts(false, $product['id_product']))) {
                continue;
            }

            $tax_rate = (float) Tax::getProductTaxRate((int) $product['id_product']);

            $coef_rate = (1 + ($tax_rate / 100));

            $detail_data = [
                'product_price' => (float) ($product['price'] / $coef_rate),
                'reduction_percent' => 0,
                'reduction_amount' => 0,
            ];

            $detail_data = array_merge($detail_data, [
                'unit_price_tax_incl' => (float) $product['price'],
                'unit_price_tax_excl' => (float) ($product['price'] / $coef_rate),
                'total_price_tax_incl' => (float) ($product['price'] * $product['quantity']),
                'total_price_tax_excl' => (float) (($product['price'] / $coef_rate) * $product['quantity']),
            ]);

            $dbEbay->autoExecute(
                _DB_PREFIX_ . 'order_detail',
                $detail_data,
                'UPDATE',
                '`id_order` = ' . (int) $this->id_orders[$ebay_profile->id_shop] . ' AND `product_id` = ' . (int) $product['id_product'] . ' AND `product_attribute_id` = ' . (int) $product['id_product_attribute']
            );

            $detail_tax_data = [
                    'unit_amount' => (float) ($product['price'] - ($product['price'] / $coef_rate)),
                    'total_amount' => ((float) ($product['price'] - ($product['price'] / $coef_rate)) * $product['quantity']),
                ];

            $dbEbay->autoExecute(_DB_PREFIX_ . 'order_detail_tax', $detail_tax_data, 'UPDATE', '`id_order_detail` = (SELECT `id_order_detail` FROM `' . _DB_PREFIX_ . 'order_detail` WHERE `id_order` = ' . (int) $this->id_orders[$ebay_profile->id_shop] . ' AND `product_id` = ' . (int) $product['id_product'] . ' AND `product_attribute_id` = ' . (int) $product['id_product_attribute'] . ') ');

            $total_price_tax_excl += (float) (($product['price'] / $coef_rate) * $product['quantity']);
        }

        $total_shipping_tax_incl += (float) $this->shippingServiceCost;
        $total_shipping_tax_excl += (float) $this->shippingServiceCost / (1 + ($carrier_tax_rate / 100));

        if ($this->payment_method == 'COD') {
            $data = [
                'total_paid' => (float) $this->amount + $this->CODCost,
                'total_paid_real' => (float) $this->amount + $this->CODCost,
                'total_products' => (float) $total_price_tax_excl,
                'total_products_wt' => (float) ($this->amount - $this->shippingServiceCost) + $this->CODCost,
                'total_shipping' => (float) $total_shipping_tax_incl,
            ];
        } else {
            $data = [
                'total_paid' => (float) $this->amount,
                'total_paid_real' => (float) $this->amount,
                'total_products' => (float) $total_price_tax_excl,
                'total_products_wt' => (float) ($this->amount - $this->shippingServiceCost),
                'total_shipping' => (float) $total_shipping_tax_incl,
            ];
        }

        $order = new Order((int) $this->id_orders[$ebay_profile->id_shop]);
        $data = array_merge(
            $data,
            [
                'total_paid_tax_excl' => (float) ($total_price_tax_excl + $total_shipping_tax_excl),
                'total_shipping_tax_incl' => (float) $total_shipping_tax_incl,
                'total_shipping_tax_excl' => (float) $total_shipping_tax_excl,
                ]
        );
        if ($this->payment_method == 'COD') {
            $data['total_paid_tax_incl'] = (float) $this->amount + $this->CODCost;
        } else {
            $data['total_paid_tax_incl'] = (float) $this->amount;
        }

        if ((float) $this->shippingServiceCost == 0) {
            $data = array_merge(
                $data,
                [
                    'total_shipping_tax_excl' => 0,
                    'total_shipping_tax_incl' => 0,
                ]
            );
        }
        // Update Incoice
        $invoice_data = $data;
        unset($invoice_data['total_paid'], $invoice_data['total_paid_real'], $invoice_data['total_shipping']);
        $dbEbay->autoExecute(_DB_PREFIX_ . 'order_invoice', $invoice_data, 'UPDATE', '`id_order` = ' . (int) $this->id_orders[$ebay_profile->id_shop]);

        // Update payment
        $payment_data = [
            'amount' => (float) $this->amount, // RAPH TODO, fix this value amount
            'payment_method' => $this->getPaymentMethod(),
            'transaction_id' => $this->id_transaction,
        ];
        $dbEbay->autoExecute(_DB_PREFIX_ . 'order_payment', $payment_data, 'UPDATE', '`order_reference` = "' . pSQL($order->reference) . '" ');

        $ship_data = [
            'shipping_cost_tax_incl' => (float) $total_shipping_tax_incl,
            'shipping_cost_tax_excl' => (float) $total_shipping_tax_excl,
        ];

        if ((float) $this->shippingServiceCost == 0) {
            $ship_data = [
                'shipping_cost_tax_incl' => 0,
                'shipping_cost_tax_excl' => 0,
            ];
        }
        $data['id_carrier'] = $id_carrier;
        $ship_data['id_carrier'] = $id_carrier;

        $dbEbay->autoExecute(_DB_PREFIX_ . 'order_carrier', $ship_data, 'UPDATE', '`id_order` = ' . (int) $this->id_orders[$ebay_profile->id_shop]);

        $orderPs = new Order((int) $this->id_orders[$ebay_profile->id_shop]);
        $orderPs->total_paid = round($data['total_paid'], 2);
        $orderPs->total_paid_real = round($data['total_paid_real'], 2);
        $orderPs->total_products = round($data['total_products'], 2);
        $orderPs->total_products_wt = round($data['total_products_wt'], 2);
        $orderPs->total_shipping = round($data['total_shipping'], 2);
        $orderPs->total_paid_tax_excl = round($data['total_paid_tax_excl'], 2);
        $orderPs->total_shipping_tax_incl = round($data['total_shipping_tax_incl'], 2);
        $orderPs->total_shipping_tax_excl = round($data['total_shipping_tax_excl'], 2);
        $orderPs->total_paid_tax_incl = round($data['total_paid_tax_incl'], 2);
        $orderPs->id_carrier = $data['id_carrier'];

        return $orderPs->save();
    }

    public function _getTaxByProduct($id_product)
    {
        $sql = 'SELECT t.`rate`
				FROM `' . _DB_PREFIX_ . 'product` AS p
				INNER JOIN `' . _DB_PREFIX_ . "tax_rule` AS tr
					ON tr.`id_tax_rules_group` = p.`id_tax_rules_group` AND tr.`id_country` = '" . (int) Country::getByIso($this->country_iso_code) . "'
				INNER JOIN `" . _DB_PREFIX_ . "tax` AS t
					ON t.`id_tax` = tr.`id_tax`
				WHERE p.`id_product` = '" . (int) $id_product . "' ";

        return DB::getInstance()->getValue($sql);
    }

    public function _getTaxByCarrier($id_carrier)
    {
        $sql = 'SELECT t.`rate`
				FROM `' . _DB_PREFIX_ . 'carrier` AS c
				INNER JOIN `' . _DB_PREFIX_ . "tax_rule` AS tr
					ON tr.`id_tax_rules_group` = c.`id_tax_rules_group` AND tr.`id_country` = '" . (int) Country::getByIso($this->country_iso_code) . "'
				INNER JOIN `" . _DB_PREFIX_ . "tax` AS t
					ON t.`id_tax` = tr.`id_tax`
				WHERE c.`id_carrier` = '" . (int) $id_carrier . "' ";

        return DB::getInstance()->getValue($sql);
    }

    /**
     * Insert the orders in the database in the table ps_ebay_order_order
     *
     * @param int|null $id_ebay_profile Ebay Profile ID's
     *
     * @throws PrestaShopDatabaseException
     */
    public function add($id_ebay_profile = null)
    {
        $this->id_ebay_order = EbayOrder::insert([
            'id_order_ref' => pSQL($this->id_order_ref),
            'id_order' => 0,
        ]);
        if ($this->id_ebay_order) {
            $this->_writeLog($id_ebay_profile, 'add_orders', $this->id_ebay_order);
        }
    }

    public function delete($id_ebay_profile = null)
    {
        db::getInstance()->execute('DELETE FROM ' . _DB_PREFIX_ . 'ebay_order WHERE id_order_ref = "' . pSQL($this->id_order_ref) . '"');

        if ($this->id_ebay_order) {
            $this->_writeLog($id_ebay_profile, 'deleted_orders', $this->id_ebay_order);
        }
    }

    public function update($id_ebay_profile = null)
    {
        if (is_array($this->id_orders)) {
            foreach ($this->id_orders as $id_shop => $id_order) {
                $res = Db::getInstance()->insert('ebay_order_order', [
                        'id_ebay_order' => (int) $this->id_ebay_order,
                        'id_order' => (int) $id_order,
                        'id_shop' => (int) $id_shop,
                        'id_ebay_profile' => ($id_ebay_profile === null) ? null : (int) $id_ebay_profile,
                        'id_transaction' => $this->id_transaction,
                    ]);
            }
            if ($res) {
                $this->_writeLog($id_ebay_profile, 'updates_order', $res);
            }
        }
    }

    public function addErrorMessage($message)
    {
        $this->error_messages[] = $message;
    }

    public function getErrorMessages()
    {
        return $this->error_messages;
    }

    public function getEmail()
    {
        return $this->email;
    }

    /**
     * @return array $this->product_list
     */
    public function getProducts()
    {
        return $this->product_list;
    }

    public function getIdOrderRef()
    {
        return $this->id_order_ref;
    }

    public function getIdOrderSeller()
    {
        return $this->id_order_seller;
    }

    public function getAmount()
    {
        return $this->amount;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function getDate()
    {
        return $this->date;
    }

    private function _parseSku($sku, $id_product, $id_product_attribute, $id_ebay_profile)
    {
        $result = [];
        preg_match('/^[a-zA-Z_-]+([\d]+)?[-_]?([\d]+)?[-_]?([\d]+)?/', $sku, $result);
        $id_product = isset($result[1]) ? $result[1] : 0;
        $id_product_attribute = isset($result[2]) ? $result[2] : 0;
        $id_ebay_profile = isset($result[3]) ? $result[3] : 0;

        return [$id_product, $id_product_attribute, $id_ebay_profile];
    }

    private function _formatShippingAddressName($name)
    {
        $name = preg_replace('/(\-)*(\d+)*(_)*(,)*(\t)*(\(.*\))*(\[.*\])*(\")*(\')*/', '', $name);
        $name = trim($name);
        $name = explode(' ', $name, 2);
        $firstname = trim(Tools::substr(trim($name[0]), 0, 32));
        $familyname = trim(isset($name[1]) ? Tools::substr(trim($name[1]), 0, 32) : Tools::substr(trim($name[0]), 0, 32));

        if (!$familyname) {
            $familyname = $firstname;
        }

        if (!$firstname) {
            $firstname = $familyname;
        }

        return [$firstname, $familyname];
    }

    private function _getReference($transaction)
    {
        $skuItem = (string) $transaction->Item->SKU;
        $skuVariation = (string) $transaction->Variation->SKU;
        $customLabel = (string) $transaction->SellingManagerProductDetails->CustomLabel;

        if ($customLabel) {
            $reference = $customLabel;
        } else {
            if ($skuVariation) {
                $reference = $skuVariation;
            } else {
                $reference = $skuItem;
            }
        }

        return trim($reference);
    }

    private function _getProductsFromTransactions($transactions)
    {
        $products = [];

        foreach ($transactions->Transaction as $transaction) {
            $id_product = 0;
            $id_product_attribute = 0;
            $id_ebay_profile = 0;
            $quantity = (string) $transaction->QuantityPurchased;

            if (isset($transaction->Item->SKU)) {
                list($id_product, $id_product_attribute, $id_ebay_profile) = $this->_parseSku($transaction->Item->SKU, $id_product, $id_product_attribute, $id_ebay_profile);
            }

            if (isset($transaction->Variation->SKU)) {
                list($id_product, $id_product_attribute, $id_ebay_profile) = $this->_parseSku($transaction->Variation->SKU, $id_product, $id_product_attribute, $id_ebay_profile);
            }

            $id_product = (int) Db::getInstance()->getValue('SELECT `id_product`
				FROM `' . _DB_PREFIX_ . 'product`
				WHERE `id_product` = ' . (int) $id_product);

            $id_product_attribute = (int) Db::getInstance()->getValue('SELECT `id_product_attribute`
				FROM `' . _DB_PREFIX_ . 'product_attribute`
				WHERE `id_product` = ' . (int) $id_product . '
				AND `id_product_attribute` = ' . (int) $id_product_attribute);

            if ($id_product) {
                $products[] = [
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'id_ebay_profile' => $id_ebay_profile,
                    'quantity' => $quantity,
                    'price' => (string) $transaction->TransactionPrice, ];
            } else {
                $product_has_find = false;
                $reference = $this->_getReference($transaction);

                if (!empty($reference)) {
                    $id_product = Db::getInstance()->getValue('SELECT `id_product`
						FROM `' . _DB_PREFIX_ . 'product`
						WHERE `reference` = \'' . pSQL($reference) . '\'');

                    if ((int) $id_product) {
                        $products[] = [
                            'id_product' => $id_product,
                            'id_product_attribute' => 0,
                            'id_ebay_profile' => 0,
                            'quantity' => $quantity,
                            'price' => (string) $transaction->TransactionPrice, ];
                        $product_has_find = true;
                    } else {
                        $row = Db::getInstance()->getRow('SELECT `id_product`, `id_product_attribute`
							FROM `' . _DB_PREFIX_ . 'product_attribute`
							WHERE `reference` = \'' . pSQL($reference) . '\'');

                        if ((int) $row['id_product']) {
                            $products[] = [
                                'id_product' => (int) $row['id_product'],
                                'id_product_attribute' => (int) $row['id_product_attribute'],
                                'id_ebay_profile' => 0,
                                'quantity' => $quantity,
                                'price' => (string) $transaction->TransactionPrice, ];
                            $product_has_find = true;
                        }
                    }
                }

                if (!$product_has_find) {
                    //Not possible with ebay multivariation products to retrieve the correct product
                    if (!isset($transaction->Variation->SKU) && !isset($transaction->Item->SKU)) {
                        if ($p = EbayProduct::getProductsIdFromItemId($transaction->Item->ItemID)) {
                            $products[] = [
                                'id_product' => $p['id_product'],
                                'id_product_attribute' => $p['id_product_attribute'],
                                'id_ebay_profile' => 0,
                                'quantity' => $quantity,
                                'price' => (string) $transaction->TransactionPrice,
                            ];
                        }
                    }
                }
            }
        }

        return $products;
    }

    /**
     * Sends an email when the minimal quantity of a product to order is not met
     *
     * @param string $product_name
     * @param int $minimal_quantity Minimal quantity to place an order
     * @param int $quantity Quantity ordered
     *
     * @return array
     **/
    private function _sendMinimalQtyAlertEmail($product_name, $minimal_quantity, $quantity)
    {
        $template_vars = [
            '{name_product}' => $product_name,
            '{min_qty}' => (string) $minimal_quantity,
            '{cart_qty}' => (string) $quantity,
        ];

        Mail::Send(
            (int) Configuration::get('PS_LANG_DEFAULT'),
            'alertEbay',
            Mail::l('Product quantity', (int) Configuration::get('PS_LANG_DEFAULT')),
            $template_vars,
            (string) Configuration::get('PS_SHOP_EMAIL'),
            null,
            (string) Configuration::get('PS_SHOP_EMAIL'),
            (string) Configuration::get('PS_SHOP_NAME'),
            null,
            null,
            dirname(__FILE__) . '/../views/templates/mails/'
        );
    }

    private function _writeLog($id_ebay_profile, $type, $success, $response = null, $is_update = false)
    {
        if (!$this->write_logs) {
            return;
        }

        $log = new EbayOrderLog();
        $log->id_ebay_profile = (int) $id_ebay_profile;
        $log->id_ebay_order = (int) $this->id_ebay_order;
        $log->id_orders = implode(';', $this->id_orders);
        $log->type = (string) $type;
        $log->success = (bool) $success;

        if ($response) {
            $log->response = $response;
        }

        if ($is_update) {
            $log->date_update = date('Y-m-d H:i:s');
        }

        $log->save();
    }

    public static function getIdOrderRefByIdOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT eo.`id_order_ref`
			FROM `' . _DB_PREFIX_ . 'ebay_order` eo
			INNER JOIN `' . _DB_PREFIX_ . 'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eoo.`id_order` = ' . (int) $id_order);
    }

    public static function getIdProfilebyIdOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT eoo.`id_ebay_profile`
			FROM `' . _DB_PREFIX_ . 'ebay_order` eo
			INNER JOIN `' . _DB_PREFIX_ . 'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eoo.`id_order` = ' . (int) $id_order);
    }

    public static function insert($data)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        $dbEbay->autoExecute(_DB_PREFIX_ . 'ebay_order', $data, 'INSERT');

        return Db::getInstance()->Insert_ID();
    }

    public static function __set_state($attributes)
    {
        $ebay_order = new EbayOrder();

        foreach ($attributes as $name => $attribute) {
            $ebay_order->{$name} = $attribute;
        }

        return $ebay_order;
    }

    public static function getOrderbytransactionId($transaction_id)
    {
        $result = Db::getInstance()->executeS('SELECT id_order
			FROM `' . _DB_PREFIX_ . 'ebay_order_order`
			WHERE `id_transaction` = ' . (string) $transaction_id);

        if ($result) {
            $result['id_ebay_order'] = EbayOrder::getIdOrderRefByIdOrder((int) $result[0]['id_order']);
        }

        return $result;
    }

    public static function getAllReturns()
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_order_return_detail`');
    }

    public static function getReturnsByOrderId($order_id)
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM `' . _DB_PREFIX_ . 'ebay_order_return_detail` WHERE id_order = ' . (int) $order_id);
    }

    public static function getEbayOrder($ItemId)
    {
        $ebay = new EbayRequest();
        $orders = [];

        $page_orders = [];

        foreach ($ebay->getOrders(false, false, 1, $ItemId) as $order_xml) {
            $page_orders[] = new EbayOrder($order_xml);
        }

        $orders = array_merge($orders, $page_orders);

        return $orders;
    }

    public function checkError($error, $ebay_user_identifier, $exist = false)
    {
        $order_error = new EbayOrderErrors();
        $order_error->ebay_user_identifier = $ebay_user_identifier;
        $order_error->id_order_seller = (int) $this->id_order_seller;
        $order_error->total = (int) $this->amount;
        $order_error->date_order = $this->date;
        $order_error->id_order_ebay = pSQL($this->id_order_ref);
        $order_error->email = $this->email;
        $order_error->error = $error;
        $order_error->save();
        if (!$exist) {
            $this->delete();
        }
    }

    public static function getOrders($id_ebay_profile)
    {
        return Db::getInstance()->executeS('SELECT eoo.*, o.date_add, o.payment, c.email, o.total_paid, o.reference, o.id_order
			FROM `' . _DB_PREFIX_ . 'ebay_order_order` eoo
			LEFT JOIN ' . _DB_PREFIX_ . 'orders o ON eoo.id_order=o.id_order
			LEFT JOIN ' . _DB_PREFIX_ . 'customer c ON o.id_customer=c.id_customer
			WHERE eoo.`id_order` > 0 AND eoo.id_ebay_profile = ' . $id_ebay_profile . ' ORDER BY o.date_add DESC');
    }

    public static function getOrderByOrderRef($id_order_ref)
    {
        return Db::getInstance()->executeS('SELECT eoo.*
			FROM `' . _DB_PREFIX_ . 'ebay_order` eo
			INNER JOIN `' . _DB_PREFIX_ . 'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eo.`id_order_ref` = ' . (int) $id_order_ref);
    }

    public static function getSumOrders($count_days = 'P30D')
    {
        $now = new DateTime();
        $necessary_date = $now->sub(new DateInterval($count_days));
        $period = $necessary_date->format('Y-m-d H:i:s');

        return Db::getInstance()->executeS('SELECT ROUND(SUM(o.`total_paid`), 2) as sum
			FROM `' . _DB_PREFIX_ . 'ebay_order_order` eo
			LEFT JOIN `' . _DB_PREFIX_ . 'orders` o ON eo.`id_order` = o.`id_order`
			WHERE eo.`id_order` > 0 AND o.`date_add`>"' . $period . '"');
    }

    public static function getPaginated($orders, $currentPage, $length)
    {
        $final_array = $orders;
        $final_array_count = count($orders);

        $pages_all = ceil(((int) ($final_array_count)) / ((int) $length));
        $range = 3;
        $start = $currentPage - $range;
        if ($start <= 0) {
            $start = 1;
        }
        $stop = $currentPage + $range;

        if ($stop > $pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int) $currentPage - 1;
        $next_page = (int) $currentPage + 1;
        $tpl_include = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/pagination.tpl';
        $vars = [];

        $vars['all_orders'] = array_slice($final_array, ($currentPage - 1) * $length, $length);
        $vars['count'] = $final_array_count;
        $vars['prev_page'] = $prev_page;
        $vars['next_page'] = $next_page;
        $vars['tpl_include'] = $tpl_include;
        $vars['pages_all'] = $pages_all;
        $vars['page_current'] = $currentPage;
        $vars['start'] = $start;
        $vars['stop'] = $stop;

        return $vars;
    }

    public static function getPaginatedOrders($id_ebay_profile, $currentPage = 1, $length = 20)
    {
        $ordersSql = self::getOrders($id_ebay_profile);
        $orders = [];
        if (!empty($ordersSql)) {
            foreach ($ordersSql as $ord) {
                $orders[] = [
                    'date_ebay' => $ord['date_add'],
                    'reference_ebay' => EbayOrder::getIdOrderRefByIdOrder($ord['id_order']),
                    'referance_marchand' => $ord['payment'],
                    'email' => $ord['email'],
                    'total' => $ord['total_paid'],
                    'id_prestashop' => $ord['id_order'],
                    'reference_ps' => $ord['reference'],
                    'date_import' => $ord['date_add'],
                    'order_link' => Context::getContext()->link->getAdminLink('AdminOrders', true, [], [
                            'vieworder' => 1,
                            'id_order' => (int) $ord['id_order'],
                        ]),
                ];
            }
        }
        $vars = self::getPaginated($orders, $currentPage, $length);

        return $vars;
    }

    public static function getPaginatedErrors($id_ebay_profile, $currentPage = 1, $length = 20)
    {
        $ordersErrorSql = EbayOrderErrors::getAll($id_ebay_profile);
        $ordersErrors = [];
        if (!empty($ordersErrorSql)) {
            foreach ($ordersErrorSql as $order_er) {
                $ordersErrors[] = [
                    'date_ebay' => $order_er['date_order'],
                    'reference_ebay' => $order_er['id_order_ebay'],
                    'referance_marchand' => $order_er['id_order_seller'],
                    'email' => $order_er['email'],
                    'total' => $order_er['total'],
                    'error' => $order_er['error'],
                    'date_import' => $order_er['date_add'],
                ];
            }
        }
        $vars = self::getPaginated($ordersErrors, $currentPage, $length);

        return $vars;
    }

    public static function getPaginatedOrderReturns($currentPage = 1, $length = 20)
    {
        $orderReturns = self::getAllReturns();
        $final_array_count = count($orderReturns);

        $pages_all = ceil(((int) ($final_array_count)) / ((int) $length));
        $range = 3;
        $start = $currentPage - $range;
        if ($start <= 0) {
            $start = 1;
        }
        $stop = $currentPage + $range;

        if ($stop > $pages_all) {
            $stop = $pages_all;
        }

        $prev_page = (int) $currentPage - 1;
        $next_page = (int) $currentPage + 1;
        $tpl_include = _PS_MODULE_DIR_ . 'ebay/views/templates/hook/pagination.tpl';

        $vars = [];
        $vars['returns'] = array_slice($orderReturns, ($currentPage - 1) * $length, $length);
        $vars['count'] = $final_array_count;
        $vars['prev_page'] = $prev_page;
        $vars['next_page'] = $next_page;
        $vars['tpl_include'] = $tpl_include;
        $vars['pages_all'] = $pages_all;
        $vars['page_current'] = $currentPage;
        $vars['start'] = $start;
        $vars['stop'] = $stop;

        return $vars;
    }

    public static function deletingInjuredOrders()
    {
        $delete = 'DELETE eo.* FROM ' . _DB_PREFIX_ . 'ebay_order eo
                    LEFT JOIN ' . _DB_PREFIX_ . 'ebay_order_order eoo ON eo.id_ebay_order = eoo.id_ebay_order
                    WHERE eoo.id_ebay_order_order IS NULL
                    LIMIT 100';

        return DB::getInstance()->execute($delete);
    }

    public function updateOrderState($ebay_profile)
    {
        $id_order = (int) $this->id_orders[$ebay_profile->id_shop];
        $orderHistory = new OrderHistory();
        $id_orderState = (int) Configuration::get('PS_OS_PAYMENT');
        $orderHistory->changeIdOrderState($id_orderState, $id_order);
    }

    public function getPaymentMethod()
    {
        $paymentMethod = 'eBay ' . $this->payment_method . ' ' . $this->id_order_seller;

        if ($this->getIOSS()) {
            $paymentMethod .= ' - IOSS ' . $this->getIOSS();
        }

        return $paymentMethod;
    }

    /**
     * @param SimpleXMLElement $order_xml
     *
     * @return string
     */
    protected function getIOSSFromResponse(SimpleXMLElement $order_xml)
    {
        $ioss = '';

        if (false == isset($order_xml->TransactionArray)) {
            return $ioss;
        }

        if (false == isset($order_xml->TransactionArray->Transaction)) {
            return $ioss;
        }

        foreach ($order_xml->TransactionArray->Transaction as $transaction) {
            if (false == isset($transaction->eBayCollectAndRemitTaxes)) {
                continue;
            }

            if (false == isset($transaction->eBayCollectAndRemitTaxes->eBayReference)) {
                continue;
            }

            if (Tools::strtolower($transaction->eBayCollectAndRemitTaxes->eBayReference['name']) == 'ioss') {
                return (string) $transaction->eBayCollectAndRemitTaxes->eBayReference;
            }
        }

        return $ioss;
    }

    /**
     * @return string
     */
    public function getIOSS()
    {
        return $this->ioss;
    }

    public function getIdEbayProfile()
    {
        return (int) $this->idEbayProfile;
    }

    protected function getProfileByItemId($order_xml)
    {
        if (false == empty($order_xml->TransactionArray->Transaction->Item->ItemID)) {
            $itemId = $order_xml->TransactionArray->Transaction->Item->ItemID;
            $sql = (new DbQuery())
                ->from('ebay_product')
                ->select('id_ebay_profile')
                ->where('id_product_ref = \'' . pSQL($itemId) . '\'');

            return (int) Db::getInstance()->getValue($sql);
        }

        return 0;
    }

    protected function getProfileBySku($order_xml)
    {
        $sku = false;

        if (false == empty($order_xml->TransactionArray->Transaction->Item->SKU)) {
            $sku = $order_xml->TransactionArray->Transaction->Item->SKU;
        }

        if (false == empty($order_xml->TransactionArray->Transaction->Variation->SKU)) {
            $sku = $order_xml->TransactionArray->Transaction->Variation->SKU;
        }

        if ($sku) {
            list($id_product, $id_product_attribute, $id_ebay_profile) = $this->_parseSku($sku, 0, 0, 0);

            return (int) $id_ebay_profile;
        }

        return 0;
    }
}
