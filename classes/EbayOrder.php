<?php
/**
 * 2007-2017 PrestaShop
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
 *  @author    PrestaShop SA <contact@prestashop.com>
 *  @copyright 2007-2017 PrestaShop SA
 *  @license   http://opensource.org/licenses/afl-3.0.php  Academic Free License (AFL 3.0)
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
    private $id_transaction;

    private $error_messages = array();

    private $write_logs;

    /* PS variables */
    private $id_customers;
    private $id_address;
    private $id_orders;
    /** @var $carts Cart[] */
    private $carts;

    public function __construct(SimpleXMLElement $order_xml = null)
    {
        if (!$order_xml) {
            return;
        }

        /** Backward compatibility */
        require dirname(__FILE__).'/../backward_compatibility/backward.php';

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

        $amount_paid_attr = $order_xml->AmountPaid->attributes();
        $this->id_currency = Currency::getIdByIsoCode($amount_paid_attr['currencyID']);

        if ($order_xml->CheckoutStatus->PaymentMethod == 'COD' && isset($order_xml->ShippingDetails->CODCost)) {
            $this->shippingServiceCost += $order_xml->ShippingDetails->CODCost;
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

        $this->id_orders = array();

        $date = Tools::substr((string) $order_xml->CreatedTime, 0, 10).' '.Tools::substr((string) $order_xml->CreatedTime, 11, 8);
        $this->date = $date;
        $this->date_add = $date;

        if ($order_xml->TransactionArray->Transaction) {
            $this->product_list = $this->_getProductsFromTransactions($order_xml->TransactionArray->Transaction);
        }

        $this->write_logs = (bool) Configuration::get('EBAY_ACTIVATE_LOGS');
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

    /**
     * Define if order has a country active in Prestashop configuration.
     * @return bool
     */
    public function isCountryEnable()
    {
        if (!Tools::isEmpty($this->country_iso_code)) {
            $country = new Country(Country::getByIso($this->country_iso_code), (int)Configuration::get('PS_LANG_DEFAULT'));

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
     * @return bool
     */
    public function exists()
    {
        return (boolean) Db::getInstance()->getValue('SELECT `id_ebay_order`
			FROM `'._DB_PREFIX_.'ebay_order`
			WHERE `id_order_ref` = \''.pSQL($this->id_order_ref).'\'');
    }

    /**
     * Define if order has a valid email, firstname, and familyname
     * @return bool
     */
    public function hasValidContact()
    {
        return Validate::isEmail($this->email)
        && $this->firstname
        && $this->familyname;
    }

    public function getOrAddCustomer($ebay_profile)
    {
        $id_customer = (int) Db::getInstance()->getValue('SELECT `id_customer`
			FROM `'._DB_PREFIX_.'customer`
			WHERE `active` = 1
			AND `email` = \''.pSQL($this->email).'\'
			AND `id_shop` = '.(int) $ebay_profile->id_shop.'
			AND `deleted` = 0'.(Tools::substr(_PS_VERSION_, 0, 3) == '1.3' ? '' : ' AND `is_guest` = 0'));

        $format = new TotFormat();

        // Add customer if he doesn't exist
        //if ($id_customer < 1) RAPH
        if (!$id_customer) {
            $customer = new Customer();
            $customer->id_gender = 0;
            $customer->id_default_group = 1;
            $customer->secure_key = md5(uniqid(rand(), true));
            $customer->email = $format->formatEmail($this->email);
            $customer->passwd = md5(_COOKIE_KEY_.rand());
            $customer->last_passwd_gen = date('Y-m-d H:i:s');
            $customer->newsletter = 0;
            if (empty($this->familyname)) {
                $this->familyname = 'no-send';
            }
            $customer->lastname = $format->formatName(EbayOrder::_formatFamilyName($this->familyname));
            $customer->firstname = $format->formatName($this->firstname);
            $customer->active = 1;
            $customer->optin = 0;
            $customer->id_shop = (int) $ebay_profile->id_shop;
            $res = $customer->add();

            $this->_writeLog($ebay_profile->id, 'add_customer', $res);

            $id_customer = $customer->id;
        }

        $this->id_customers[$ebay_profile->id_shop] = $id_customer;

        return $id_customer;
    }

    public function updateOrAddAddress($ebay_profile)
    {
        // Search if address exists
        $id_address = (int) Db::getInstance()->getValue('SELECT `id_address`
			FROM `'._DB_PREFIX_.'address`
			WHERE `id_customer` = '.(int) $this->id_customers[$ebay_profile->id_shop].'
			AND `alias` = \'eBay\'');

        if ($id_address) {
            $address = new Address((int) $id_address);
        } else {
            $address = new Address();
            $address->id_customer = (int) $this->id_customers[$ebay_profile->id_shop];
        }

        $format = new TotFormat();

        $address->id_country = (int) Country::getByIso($this->country_iso_code);
        $address->alias = 'eBay';
        $address->lastname = $format->formatName(EbayOrder::_formatFamilyName($this->familyname));
        $address->firstname = $format->formatName($this->firstname);
        $address->address1 = $format->formatAddress($this->address1);
        $address->address2 = $format->formatAddress($this->address2);
        $address->postcode = $format->formatPostCode(str_replace('.', '', $this->postalcode));
        $address->city = $format->formatCityName($this->city);
        
        if (!empty($this->phone)) {
            $address->phone = $format->formatPhoneNumber($this->phone);
            $address->phone_mobile = $format->formatPhoneNumber($this->phone);
        }

        $address->active = 1;

        if ($id_address > 0 && Validate::isLoadedObject($address)) {
            $res = $address->update();
            $is_update = true;
        } else {
            $res = $address->add();
            $id_address = $address->id;
            $is_update = false;
        }

        $this->_writeLog($ebay_profile->id, 'add_address', $res, null, $is_update);

        $this->id_address = $id_address;

        return $id_address;
    }

    /**
     * Formats the family name to match eBay constraints:
     * - length < 32 chars
     * - no brackets ()
     * @param $family_name
     * @return mixed
     */
    private function _formatFamilyName($family_name)
    {
        return str_replace(array('(', ')'), '', Tools::substr(pSQL($family_name), 0, 32));
    }

    public function getProductIds()
    {
        return array_map(create_function('$product', 'return (int)$product[\'id_product\'];'), $this->product_list);
    }

    /**
     * Get eBay profile and Products and attribute by shop
     * @return array All product in an array like
     *      $res[$id_shop]
     *          ['id_ebay_profiles']
     *               [0]
     *               [1]
     *          ['id_products']
     *               [0]
     *                  ['id_product']
     *                  ['id_product_attribute']
     *               [1]
     *                  ['id_product']
     *                  ['id_product_attribute']
     */
    public function getProductsAndProfileByShop()
    {
        $res = array();
        foreach ($this->product_list as $product) {
            if ($product['id_ebay_profile']) {
                $ebay_profile = new EbayProfile((int)$product['id_ebay_profile']);
            } else {
                $sql = 'SELECT epr.`id_ebay_profile`
				FROM `'._DB_PREFIX_.'ebay_product` epr
				WHERE epr.`id_product` = '.(int)$product['id_product'];
                $id_ebay_profile = (int)Db::getInstance()->getValue($sql);
                if ($id_ebay_profile) {
                    $ebay_profile = new EbayProfile($id_ebay_profile);
                } else {
                    // case where the row in ebay_product has disappeared like if the product has been removed from eBay before sync
                    if (Shop::isFeatureActive()) {
                        $sql = 'SELECT `id_ebay_profile`
							FROM `'._DB_PREFIX_.'ebay_profile` ep
							LEFT JOIN `'._DB_PREFIX_.'product_shop` ps
							ON ep.`id_shop` = ps.`id_shop`
							AND ps.`id_product` = '.(int)$product['id_product'];
                        $id_ebay_profile = (int)Db::getInstance()->getValue($sql);
                        $ebay_profile = new EbayProfile($id_ebay_profile);
                    } else {
                        $ebay_profile = EbayProfile::getCurrent();
                    }
                }
            }

            if (!isset($res[$ebay_profile->id_shop])) {
                $res[$ebay_profile->id_shop] = array(
                    'id_ebay_profiles' => array($ebay_profile->id),
                    'id_products'      => array(),
                );
            } elseif (!in_array($ebay_profile->id, $res[$ebay_profile->id_shop]['id_ebay_profiles'])) {
                $res[$ebay_profile->id_shop]['id_ebay_profiles'][] = $ebay_profile->id;
            }

            // $res[$ebay_profile->id_shop]['id_products'][] = $product['id_product'];
            $res[$ebay_profile->id_shop]['id_products'][] = array(
                'id_product'           => $product['id_product'],
                'id_product_attribute' => $product['id_product_attribute']
            );
        }

        return $res;
    }

    /**
     * Define if product and attribute exist in Prestashop.
     * @return bool
     */
    public function hasAllProductsWithAttributes()
    {
        foreach ($this->product_list as $product) {
            if ((int)$product['id_product'] < 1
                || !Db::getInstance()->getValue('SELECT `id_product`
					FROM `'._DB_PREFIX_.'product`
					WHERE `id_product` = '.(int)$product['id_product'])
            ) {
                return false;
            }

            if (isset($product['id_product_attribute'])
                && $product['id_product_attribute'] > 0
                && !Db::getInstance()->getValue('SELECT `id_product_attribute`
					FROM `'._DB_PREFIX_.'product_attribute`
					WHERE `id_product` = '.(int)$product['id_product'].'
					AND `id_product_attribute` = '.(int)$product['id_product_attribute'])
            ) {
                return false;
            }
        }

        return true;
    }

    /**
     * Add a new Cart with order content, and save it in database
     * @param EbayProfile     $ebay_profile
     * @param EbayCountrySpec $ebay_country
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
        $cart->delivery_option = @serialize(array($this->id_address => $id_carrier.','));
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
            $product_list = array();
        }

        // foreach ($product_list as $id_product)
        // {
        //     foreach($this->product_list as $product)
        //         if ($id_product == $product['id_product'])
        //             break;
        foreach ($product_list as $p) {
//Nombre de produit donc nombre de tour
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
                        new Shop($ebay_profile->id_shop)
                    );

                    if ($update === true) {
                        $cart_nb_products++;
                    }



            } else {
                // minimal quantity for purchase not met
                $this->_sendMinimalQtyAlertEmail($prod->name, $minimal_quantity, $product['quantity']);
            }

        }

        $this->carts[$ebay_profile->id_shop]->update();

        $this->carts[$ebay_profile->id_shop]->getProducts(true);
        $this->carts[$ebay_profile->id_shop]->getDeliveryOptionList(null, true);


        return (boolean) $cart_nb_products;
    }

    /**
     * Validate an order in database
     * @param int      $id_shop
     * @param int|null $id_ebay_profile
     * @return int Current order's id
     * @throws PrestaShopDatabaseException
     * @throws PrestaShopException
     */
    public function validate($id_shop, $id_ebay_profile = null)
    {
        $customer = new Customer($this->id_customers[$id_shop]);
        $payment = new EbayPayment();

        //Change context's currency
        $this->context->currency = new Currency($this->carts[$id_shop]->id_currency);
        try {
            $payment->validateOrder(
                (int) $this->carts[$id_shop]->id,
                Configuration::get('PS_OS_PAYMENT'),
                (float) $this->carts[$id_shop]->getOrderTotal(true, Cart::BOTH),
                'eBay '.$this->payment_method.' '.$this->id_order_seller,
                null,
                array(),
                (int) $this->carts[$id_shop]->id_currency,
                false,
                $customer->secure_key,
                new Shop((int) $id_shop)
            );
        } catch (Exception $e) {
            $this->_writeLog($id_ebay_profile, $e->getMessage(), false, 'End of validate order FAIL!');
            $this->delete();

        }


        $this->id_orders[$id_shop] = $payment->currentOrder;

        $this->_writeLog($id_ebay_profile, 'validate_order', true, 'End of validate order');

        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        // Fix on date
        $dbEbay->autoExecute(_DB_PREFIX_.'orders', array('date_add' => pSQL($this->date_add)), 'UPDATE', '`id_order` = '.(int) $this->id_orders[$id_shop]);

        return $payment->currentOrder;
    }

    /**
     * Update price of the current Order
     * @param EbayProfile $ebay_profile
     * @return bool
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

            $detail_data = array(
                'product_price' => (float) ($product['price'] / $coef_rate),
                'reduction_percent' => 0,
                'reduction_amount' => 0,
            );


                $detail_data = array_merge($detail_data, array(
                    'unit_price_tax_incl' => (float) $product['price'],
                    'unit_price_tax_excl' => (float) ($product['price'] / $coef_rate),
                    'total_price_tax_incl' => (float) ($product['price'] * $product['quantity']),
                    'total_price_tax_excl' => (float) (($product['price'] / $coef_rate) * $product['quantity']),
                ));


            $dbEbay->autoExecute(
                _DB_PREFIX_.'order_detail',
                $detail_data,
                'UPDATE',
                '`id_order` = '.(int) $this->id_orders[$ebay_profile->id_shop].' AND `product_id` = '.(int) $product['id_product'].' AND `product_attribute_id` = '.(int) $product['id_product_attribute']
            );

                $detail_tax_data = array(
                    'unit_amount' => (float) ($product['price'] - ($product['price'] / $coef_rate)),
                    'total_amount' => ((float) ($product['price'] - ($product['price'] / $coef_rate)) * $product['quantity']),
                );

                $dbEbay->autoExecute(_DB_PREFIX_.'order_detail_tax', $detail_tax_data, 'UPDATE', '`id_order_detail` = (SELECT `id_order_detail` FROM `'._DB_PREFIX_.'order_detail` WHERE `id_order` = '.(int) $this->id_orders[$ebay_profile->id_shop].' AND `product_id` = '.(int) $product['id_product'].' AND `product_attribute_id` = '.(int) $product['id_product_attribute'].') ');


            $total_price_tax_excl += (float) (($product['price'] / $coef_rate) * $product['quantity']);
        }

        $total_shipping_tax_incl += (float)$this->shippingServiceCost;
        $total_shipping_tax_excl += $this->shippingServiceCost / (1 + ($carrier_tax_rate / 100));

        $data = array(
            'total_paid' => (float) $this->amount,
            'total_paid_real' => (float) $this->amount,
            'total_products' => (float) $total_price_tax_excl,
            'total_products_wt' => (float) ($this->amount - $this->shippingServiceCost),
            'total_shipping' => (float) $total_shipping_tax_incl,

        );


            $order = new Order((int) $this->id_orders[$ebay_profile->id_shop]);


            $data = array_merge(
                $data,
                array(
                    'total_paid_tax_incl' => (float) $this->amount,
                    'total_paid_tax_excl' => (float) ($total_price_tax_excl + $total_shipping_tax_excl),
                    'total_shipping_tax_incl' => (float) $total_shipping_tax_incl,
                    'total_shipping_tax_excl' => (float) $total_shipping_tax_excl,
                )
            );

            if ((float) $this->shippingServiceCost == 0) {
                $data = array_merge(
                    $data,
                    array(
                        'total_shipping_tax_excl' => 0,
                        'total_shipping_tax_incl' => 0,
                    )
                );

            }
            // Update Incoice
            $invoice_data = $data;
            unset($invoice_data['total_paid'], $invoice_data['total_paid_real'], $invoice_data['total_shipping']);
            $dbEbay->autoExecute(_DB_PREFIX_.'order_invoice', $invoice_data, 'UPDATE', '`id_order` = '.(int) $this->id_orders[$ebay_profile->id_shop]);
           // Update payment
            $payment_data = array(
                'amount' => (float) $this->amount, // RAPH TODO, fix this value amount
            );
            $dbEbay->autoExecute(_DB_PREFIX_.'order_payment', $payment_data, 'UPDATE', '`order_reference` = "'.pSQL($order->reference).'" ');



            $ship_data = array(
                'shipping_cost_tax_incl' => (float) $total_shipping_tax_incl,
                'shipping_cost_tax_excl' => (float) $total_shipping_tax_excl,
            );

            if ((float) $this->shippingServiceCost == 0) {

                $ship_data = array(
                    'shipping_cost_tax_incl' => 0,
                    'shipping_cost_tax_excl' => 0,
                );
            }
            $dbEbay->autoExecute(_DB_PREFIX_.'order_carrier', $ship_data, 'UPDATE', '`id_order` = '.(int) $this->id_orders[$ebay_profile->id_shop]);

        return $dbEbay->autoExecute(_DB_PREFIX_.'orders', $data, 'UPDATE', '`id_order` = '.(int) $this->id_orders[$ebay_profile->id_shop]);
    }

    public function _getTaxByProduct($id_product)
    {
        $sql = "SELECT t.`rate`
				FROM `"._DB_PREFIX_."product` AS p
				INNER JOIN `"._DB_PREFIX_."tax_rule` AS tr
					ON tr.`id_tax_rules_group` = p.`id_tax_rules_group` AND tr.`id_country` = '".(int) Country::getByIso($this->country_iso_code)."'
				INNER JOIN `"._DB_PREFIX_."tax` AS t
					ON t.`id_tax` = tr.`id_tax`
				WHERE p.`id_product` = '".(int) $id_product."' ";

        return DB::getInstance()->getValue($sql);
    }

    public function _getTaxByCarrier($id_carrier)
    {
        $sql = "SELECT t.`rate`
				FROM `"._DB_PREFIX_."carrier` AS c
				INNER JOIN `"._DB_PREFIX_."tax_rule` AS tr
					ON tr.`id_tax_rules_group` = c.`id_tax_rules_group` AND tr.`id_country` = '".(int) Country::getByIso($this->country_iso_code)."'
				INNER JOIN `"._DB_PREFIX_."tax` AS t
					ON t.`id_tax` = tr.`id_tax`
				WHERE c.`id_carrier` = '".(int) $id_carrier."' ";

        return DB::getInstance()->getValue($sql);
    }

    /**
     * Insert the orders in the database in the table ps_ebay_order_order
     * @param int|null $id_ebay_profile Ebay Profile ID's
     * @throws PrestaShopDatabaseException
     */
    public function add($id_ebay_profile = null)
    {
        $this->id_ebay_order = EbayOrder::insert(array(
            'id_order_ref' => pSQL($this->id_order_ref),
        ));
        if ($this->id_ebay_order) {
            $this->_writeLog($id_ebay_profile, 'add_orders', $this->id_ebay_order);
        }
    }

    public function delete($id_ebay_profile = null)
    {
        db::getInstance()->execute('DELETE FROM '._DB_PREFIX_.'ebay_order WHERE id_order_ref = "'.pSQL($this->id_order_ref).'"');

        if ($this->id_ebay_order) {
            $this->_writeLog($id_ebay_profile, 'deleted_orders', $this->id_ebay_order);
        }
    }

    public function update($id_ebay_profile = null)
    {
        if (is_array($this->id_orders)) {
            foreach ($this->id_orders as $id_shop => $id_order) {
                    $res = Db::getInstance()->insert('ebay_order_order', array(
                        'id_ebay_order' => (int) $this->id_ebay_order,
                        'id_order' => (int) $id_order,
                        'id_shop' => (int) $id_shop,
                        'id_ebay_profile' => ($id_ebay_profile === null) ? null : (int) $id_ebay_profile,
                        'id_transaction' => $this->id_transaction,
                    ));
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
        $data = explode('-', (string) $sku);

        if (isset($data[1])) {
            $id_product = $data[1];
        }

        if (isset($data[2])) {
            $data2 = explode('_', $data[2]);
            $id_product_attribute = $data2[0];
            if (isset($data2[1])) {
                $id_ebay_profile = (int) $data2[1];
            }
        }

        return array($id_product, $id_product_attribute, $id_ebay_profile);
    }

    private function _formatShippingAddressName($name)
    {
        $name = str_replace(array('_', ',', '  '), array('', '', ' '), (string) $name);
        $name = preg_replace('/\-?\d+/', '', $name);
        $name = explode(' ', $name, 2);
        $firstname = trim(Tools::substr(trim($name[0]), 0, 32));
        $familyname = trim(isset($name[1]) ? Tools::substr(trim($name[1]), 0, 32) : Tools::substr(trim($name[0]), 0, 32));

        if (!$familyname) {
            $familyname = $firstname;
        }

        if (!$firstname) {
            $firstname = $familyname;
        }

        return array($firstname, $familyname);
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
        $products = array();

        foreach ($transactions as $transaction) {
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
				FROM `'._DB_PREFIX_.'product`
				WHERE `id_product` = '.(int) $id_product);

            $id_product_attribute = (int) Db::getInstance()->getValue('SELECT `id_product_attribute`
				FROM `'._DB_PREFIX_.'product_attribute`
				WHERE `id_product` = '.(int) $id_product.'
				AND `id_product_attribute` = '.(int) $id_product_attribute);

            if ($id_product) {
                $products[] = array(
                    'id_product' => $id_product,
                    'id_product_attribute' => $id_product_attribute,
                    'id_ebay_profile' => $id_ebay_profile,
                    'quantity' => $quantity,
                    'price' => (string) $transaction->TransactionPrice);
            } else {
                $product_has_find = false;
                $reference = $this->_getReference($transaction);

                if (!empty($reference)) {
                    $id_product = Db::getInstance()->getValue('SELECT `id_product`
						FROM `'._DB_PREFIX_.'product`
						WHERE `reference` = \''.pSQL($reference).'\'');

                    if ((int) $id_product) {
                        $products[] = array(
                            'id_product' => $id_product,
                            'id_product_attribute' => 0,
                            'id_ebay_profile' => 0,
                            'quantity' => $quantity,
                            'price' => (string) $transaction->TransactionPrice);
                        $product_has_find = true;
                    } else {
                        $row = Db::getInstance()->getRow('SELECT `id_product`, `id_product_attribute`
							FROM `'._DB_PREFIX_.'product_attribute`
							WHERE `reference` = \''.pSQL($reference).'\'');

                        if ((int) $row['id_product']) {
                            $products[] = array(
                                'id_product' => (int) $row['id_product'],
                                'id_product_attribute' => (int) $row['id_product_attribute'],
                                'id_ebay_profile' => 0,
                                'quantity' => $quantity,
                                'price' => (string) $transaction->TransactionPrice);
                            $product_has_find = true;
                        }
                    }
                }

                if (!$product_has_find && false) {
                    //Not possible with ebay multivariation products to retrieve the correct product
                    if (!isset($transaction->Variation->SKU)) {
                        if ($p = EbayProduct::getProductsIdFromItemId($transaction->Item->ID)) {
                            $products[] = array(
                                'id_product' => $p['id_product'],
                                'id_product_attribute' => $p['id_product_attribute'],
                                'id_ebay_profile' => 0,
                                'quantity' => $quantity,
                                'price' => (string) $transaction->TransactionPrice);
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
     * @return array
     **/
    private function _sendMinimalQtyAlertEmail($product_name, $minimal_quantity, $quantity)
    {
        $template_vars = array(
            '{name_product}' => $product_name,
            '{min_qty}' => (string) $minimal_quantity,
            '{cart_qty}' => (string) $quantity,
        );

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
            dirname(__FILE__).'/../views/templates/mails/'
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
			FROM `'._DB_PREFIX_.'ebay_order` eo
			INNER JOIN `'._DB_PREFIX_.'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eoo.`id_order` = '.(int) $id_order);
    }

    public static function getIdProfilebyIdOrder($id_order)
    {
        return Db::getInstance()->getValue('SELECT eoo.`id_ebay_profile`
			FROM `'._DB_PREFIX_.'ebay_order` eo
			INNER JOIN `'._DB_PREFIX_.'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eoo.`id_order` = '.(int) $id_order);
    }

    public static function insert($data)
    {
        $dbEbay = new DbEbay();
        $dbEbay->setDb(Db::getInstance());

        $dbEbay->autoExecute(_DB_PREFIX_.'ebay_order', $data, 'INSERT');

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
			FROM `'._DB_PREFIX_.'ebay_order_order`
			WHERE `id_transaction` = '.(string) $transaction_id);

            $result['id_ebay_order']= EbayOrder::getIdOrderRefByIdOrder((int)$result[0]['id_order']);
  
        return $result;
    }
    
    public static function getAllReturns()
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM `'._DB_PREFIX_.'ebay_order_return_detail`');
    }

    public static function getReturnsByOrderId($order_id)
    {
        return Db::getInstance()->ExecuteS('SELECT *
			FROM `'._DB_PREFIX_.'ebay_order_return_detail` WHERE id_order = '.(int) $order_id);
    }

    public static function getEbayOrder($ItemId)
    {

        $ebay = new EbayRequest();
        $orders = array();

        $page_orders = array();

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

    public static function getOrders()
    {
        return Db::getInstance()->executeS('SELECT *
			FROM `'._DB_PREFIX_.'ebay_order_order`
			WHERE `id_order` > 0');
    }
    public static function getOrderByOrderRef($id_order_ref)
    {
        return Db::getInstance()->executeS('SELECT eoo.*
			FROM `'._DB_PREFIX_.'ebay_order` eo
			INNER JOIN `'._DB_PREFIX_.'ebay_order_order` eoo
			ON eo.`id_ebay_order` = eoo.`id_ebay_order`
			WHERE eo.`id_order_ref` = '.(int) $id_order_ref);
    }
}
