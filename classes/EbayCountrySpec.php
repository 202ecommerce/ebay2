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

if (!in_array('Ebay', get_declared_classes())) {
    require_once dirname(__FILE__).'/../ebay.php';
}

class EbayCountrySpec
{
    public $country;
    public $accepted_isos = array( 'es', 'fr', 'nl', 'pl', 'be','it', 'gb', 'de', 'ie', 'au');
    protected $ebay_iso;
    private $dev;
    private static $multilang = array('be');

    private static $allCountries = array(
        'AD' => 'Andorra',
        'AE' => 'United Arab Emirates',
        'AF' => 'Afghanistan',
        'AG' => 'Antigua and Barbuda',
        'AI' => 'Anguilla',
        'AL' => 'Albania',
        'AM' => 'Armenia',
        'AN' => 'Netherlands Antilles',
        'AO' => 'Angola',
        'AQ' => 'Antarctica',
        'AR' => 'Argentina',
        'AS' => 'American Samoa',
        'AT' => 'Austria',
        'AU' => 'Australia',
        'AW' => 'Aruba',
        'AZ' => 'Azerbaijan',
        'BA' => 'Bosnia and Herzegovina',
        'BB' => 'Barbados',
        'BD' => 'Bangladesh',
        'BE' => 'Belgium',
        'BF' => 'Burkina Faso',
        'BG' => 'Bulgaria',
        'BH' => 'Bahrain',
        'BI' => 'Burundi',
        'BJ' => 'Benin',
        'BM' => 'Bermuda',
        'BN' => 'Brunei Darussalam',
        'BO' => 'Bolivia',
        'BR' => 'Brazil',
        'BS' => 'Bahamas',
        'BT' => 'Bhutan',
        'BV' => 'Bouvet Island',
        'BW' => 'Botswana',
        'BY' => 'Belarus',
        'BZ' => 'Belize',
        'CA' => 'Canada',
        'CC' => 'Cocos (Keeling) Islands',
        'CD' => 'The Democratic Republic of the Congo',
        'CF' => 'Central African Republic',
        'CG' => 'Congo',
        'CH' => 'Switzerland',
        'CI' => 'Cote d\'Ivoire',
        'CK' => 'Cook Islands',
        'CL' => 'Chile',
        'CM' => 'Cameroon',
        'CN' => 'China',
        'CO' => 'Colombia',
        'CR' => 'Costa Rica',
        'CU' => 'Cuba',
        'CV' => 'Cape Verde',
        'CX' => 'Christmas Island',
        'CY' => 'Cyprus',
        'CZ' => 'Czech Republic',
        'DE' => 'Germany',
        'DJ' => 'Djibouti',
        'DK' => 'Denmark',
        'DM' => 'Dominica',
        'DO' => 'Dominican Republic',
        'DZ' => 'Algeria',
        'EC' => 'Ecuador',
        'EE' => 'Estonia',
        'EG' => 'Egypt',
        'EH' => 'Western Sahara',
        'ER' => 'Eritrea',
        'ES' => 'Spain',
        'ET' => 'Ethiopia',
        'FI' => 'Finland',
        'FJ' => 'Fiji',
        'FK' => 'Falkland Islands (Malvinas)',
        'FM' => 'Federated States of Micronesia',
        'FO' => 'Faroe Islands',
        'FR' => 'France',
        'GA' => 'Gabon',
        'GB' => 'United Kingdom',
        'GD' => 'Grenada',
        'GE' => 'Georgia',
        'GF' => 'French Guiana',
        'GG' => 'Guernsey',
        'GH' => 'Ghana',
        'GI' => 'Gibraltar',
        'GL' => 'Greenland',
        'GM' => 'Gambia',
        'GN' => 'Guinea',
        'GP' => 'Guadeloupe',
        'GQ' => 'Equatorial Guinea',
        'GR' => 'Greece',
        'GS' => 'South Georgia and the South Sandwich Islands',
        'GT' => 'Guatemala',
        'GU' => 'Guam',
        'GW' => 'Guinea-Bissau',
        'GY' => 'Guyana',
        'HK' => 'Hong Kong',
        'HM' => 'Heard Island and McDonald Islands',
        'HN' => 'Honduras',
        'HR' => 'Croatia',
        'HT' => 'Haiti',
        'HU' => 'Hungary',
        'ID' => 'Indonesia',
        'IE' => 'Ireland',
        'IL' => 'IL',
        'IN' => 'India',
        'IO' => 'British Indian Ocean Territory',
        'IQ' => 'Iraq',
        'IR' => 'Islamic Republic of Iran',
        'IS' => 'Iceland',
        'IT' => 'Italy',
        'JE' => 'Jersey',
        'JM' => 'Jamaica',
        'JO' => 'Jordan',
        'JP' => 'Japan',
        'KE' => 'Kenya',
        'KG' => 'Kyrgyzstan',
        'KH' => 'Cambodia',
        'KI' => 'Kiribati',
        'KM' => 'Comoros',
        'KN' => 'Saint Kitts and Nevis',
        'KP' => 'Democratic People\'s Republic of Korea',
        'KR' => 'Republic of Korea',
        'KW' => 'Kuwait',
        'KY' => 'Cayman Islands',
        'KZ' => 'Kazakhstan',
        'LA' => 'Lao People\'s Democratic Republic',
        'LB' => 'Lebanon',
        'LC' => 'Saint Lucia',
        'LI' => 'Liechtenstein',
        'LK' => 'Sri Lanka',
        'LR' => 'Liberia',
        'LS' => 'Lesotho',
        'LT' => 'Lithuania',
        'LU' => 'Luxembourg',
        'LV' => 'Latvia',
        'LY' => 'Libyan Arab Jamahiriya',
        'MA' => 'Morocco',
        'MC' => 'Monaco',
        'MD' => 'Republic of Moldova',
        'ME' => 'Montenegro',
        'MG' => 'Madagascar',
        'MH' => 'Marshall Islands',
        'MK' => 'The Former Yugoslav Republic of Macedonia',
        'ML' => 'Mali',
        'MM' => 'Myanmar',
        'MN' => 'Mongolia',
        'MO' => 'Macao',
        'MP' => 'Northern Mariana Islands',
        'MQ' => 'Martinique',
        'MR' => 'Mauritania',
        'MS' => 'Montserrat',
        'MT' => 'Malta',
        'MU' => 'Mauritius',
        'MV' => 'Maldives',
        'MW' => 'Malawi',
        'MX' => 'Mexico',
        'MY' => 'Malaysia',
        'MZ' => 'Mozambique',
        'NA' => 'Namibia',
        'NC' => 'New Caledonia',
        'NE' => 'Niger',
        'NF' => 'Norfolk Island',
        'NG' => 'Nigeria',
        'NI' => 'Nicaragua',
        'NL' => 'Netherlands',
        'NO' => 'Norway',
        'NP' => 'Nepal',
        'NR' => 'Nauru',
        'NU' => 'Niue',
        'NZ' => 'New Zealand',
        'OM' => 'Oman',
        'PA' => 'Panama',
        'PE' => 'Peru',
        'PF' => 'French Polynesia. Includes Tahiti',
        'PG' => 'Papua New Guinea',
        'PH' => 'Philippines',
        'PK' => 'Pakistan',
        'PL' => 'Poland',
        'PM' => 'Saint Pierre and Miquelon',
        'PN' => 'Pitcairn',
        'PR' => 'Puerto Rico',
        'PS' => 'Palestinian territory, Occupied',
        'PT' => 'Portugal',
        'PW' => 'Palau',
        'PY' => 'Paraguay',
        'QA' => 'Qatar',
        'QM' => 'Guernsey',
        'QN' => 'Jan Mayen',
        'QO' => 'Jersey',
        'RE' => 'Reunion',
        'RO' => 'Romania',
        'RS' => 'Serbia',
        'RU' => 'Russian Federation',
        'RW' => 'Rwanda',
        'SA' => 'Saudi Arabia',
        'SB' => 'Solomon Islands',
        'SC' => 'Seychelles',
        'SD' => 'Sudan',
        'SE' => 'Sweden',
        'SG' => 'Singapore',
        'SH' => 'Saint Helena',
        'SI' => 'Svalbard and Jan Mayen',
        'SK' => 'Slovakia',
        'SL' => 'Sierra Leone',
        'SM' => 'San Marino',
        'SN' => 'Senegal',
        'SO' => 'Somalia',
        'SR' => 'Suriname',
        'ST' => 'Sao Tome and Principe',
        'SV' => 'El Salvador',
        'SY' => 'Syrian Arab Republic',
        'SZ' => 'Swaziland',
        'TC' => 'Turks and Caicos Islands',
        'TD' => 'Chad',
        'TF' => 'French Southern Territories',
        'TG' => 'Togo',
        'TH' => 'Thailand',
        'TJ' => 'Tajikistan',
        'TK' => 'Tokelau',
        'TM' => 'Turkmenistan',
        'TN' => 'Tunisia',
        'TO' => 'Tonga',
        'TR' => 'Turkey',
        'TT' => 'Trinidad and Tobago',
        'TV' => 'Tuvalu',
        'TW' => 'Taiwan, Province of China',
        'TZ' => 'United Republic of Tanzania',
        'UA' => 'Ukraine',
        'UG' => 'Uganda',
        'UM' => 'United States Minor Outlying Islands',
        'US' => 'United States',
        'UY' => 'Uruguay',
        'UZ' => 'Uzbekistan',
        'VA' => 'Holy See (Vatican City state)',
        'VC' => 'Saint Vincent and the Grenadines',
        'VE' => 'Venezuela',
        'VG' => 'Virgin Islands, British',
        'VI' => 'Virgin Islands, U.S',
        'VN' => 'Vietnam',
        'VU' => 'Vanuatu',
        'WF' => 'Wallis and Futuna',
        'WS' => 'Samoa',
        'YE' => 'Yemen',
        'YT' => 'Mayotte',
        'ZA' => 'South Africa',
        'ZM' => 'Zambia',
        'ZW' => 'Zimbabwe',
    );

    public function __construct(Country $country = null)
    {
        if ($country != null) {
            $this->country = $country;
        } else {
            $this->country = $this->_getCountry();
        }
    }

    public function getTitleDescUrl()
    {
        return $this->_getCountryData('title_desc_url');
    }

    public function getSimilarItemsUrl()
    {
        return $this->_getCountryData('similar_items_url');
    }

    public function getPictureUrl()
    {
        return $this->_getCountryData('picture_url');
    }

    public function getTopRatedUrl()
    {
        return $this->_getCountryData('top_rated_url');
    }

    public function getSiteID()
    {
        return $this->_getCountryData('site_id');
    }

    public function getDocumentationLang()
    {
        return $this->_getCountryDATA('documentation');
    }

    public function getLanguage()
    {
        return $this->_getCountryData('language');
    }

    public function getCurrency()
    {
        return $this->_getCountryData('currency');
    }

    public function getSiteName()
    {
        return $this->_getCountryData('site_name');
    }

    public function getSiteExtension()
    {
        return $this->_getCountryData('site_extension');
    }

    public function getSignInProURL()
    {
        return $this->_getCountryData('signin_pro_url');
    }

    public function getSiteSubDomain()
    {
        return $this->_getCountryData('subdomain');
    }

    public function getSiteSignin()
    {
        if ($this->dev != true) {
            return $this->_getCountryData('signin');
        } else {
            return $this->_getCountryData('signin_sandbox');
        }
    }

    public function getImgStats()
    {
        return $this->_getCountryData('img_stats');
    }

    public function getIsoCode()
    {
        if (!$this->country) {
            return null;
        }

        return $this->country->iso_code;
    }

    public function getProUrl()
    {
        return $this->_getCountryData('pro_url');
    }

    public function getFeeUrl()
    {
        return $this->_getCountryData('fee_url');
    }

    public function getIdLang()
    {
        $id_lang = Language::getIdByIso($this->getIsoCode());
        if (!$id_lang) {
            //Fix for UK
            $id_lang = Configuration::get('PS_LANG_DEFAULT');
        }

        return (int) $id_lang;
    }
    public function getHelpUrlBusinesss()
    {
         return $this->_getCountryData('url_help_business_policies');
    }

    private function _getCountryData($data)
    {
        $iso_code = $this->ebay_iso;
        if (isset(self::getCountryData()[$iso_code]) && isset(self::getCountryData()[$iso_code][$data])) {
            return self::getCountryData()[$iso_code][$data];
        } else if (isset(self::getCountryData()['fr'][$data])) {
            return self::getCountryData()['fr'][$data];
        } else {
            return null;
        }
    }

    /**
     * Tools Methods
     *
     * Sends back true or false
     **/
    public function checkCountry()
    {
        if (in_array(Tools::strtolower($this->country->iso_code), $this->accepted_isos)) {
            return true;
        }

        return false;
    }

    /**
     * Tools Methods
     *
     * Set country
     *
     **/
    private function _getCountry()
    {
        $ebay_profile = EbayProfile::getCurrent();
        if ($ebay_profile) {
            $ebayCountry = self::getInstanceByKey($ebay_profile->getConfiguration('EBAY_COUNTRY_DEFAULT'));
        } else {
            $ebayCountry = self::getInstanceByKey('gb');
        }

        $this->country = $ebayCountry->country;

        return $this->country;
    }

    /**
     * Get countries
     * @param bool $dev
     * @return array Countries list
     */
    public static function getCountries($dev)
    {
        $countries = array();

        foreach (self::getCountryData() as $iso => $ctry) {
            if (isset($ctry['subdomain']) === false) {
                $ctry['subdomain'] = null;
            }
            if ($dev) {
                unset($ctry['signin']);
                $ctry['signin'] = $ctry['signin_sandbox'];
            }
            $countries[$iso] = $ctry;
        }

        ksort($countries);

        return $countries;
    }


    /**
     * Get countries
     * @param bool $dev
     * @return array Countries list
     */
    public static function getCountriesSelect($dev)
    {
        asort(self::$allCountries);
        return self::$allCountries;
    }

    /**
     * Get Instance for Ebay Country
     * @param  string          $key Key of country
     * @param  boolean         $dev If module work in debug
     * @return EbayCountrySpec Ebay country
     */
    public static function getInstanceByKey($key, $dev = false)
    {
        
        if (isset(self::getCountryData()[$key])) {
            $iso_code = self::getCountryData()[$key]['iso_code'];
            $id_country = Country::getByIso($iso_code);
        } else {
            $id_country = Configuration::get('PS_COUNTRY_DEFAULT');
        }
        
        $ebay_country = new EbayCountrySpec(new Country($id_country));
        $ebay_country->setDev($dev);
        $ebay_country->ebay_iso = is_numeric($key) ? self::getKeyForEbayCountry() : $key;

        return $ebay_country;
    }

    public static function getInstanceByCountryAndLang($iso_country, $iso_lang)
    {
        if (isset(self::getCountryData()[$iso_country])) {
            return self::getInstanceByKey($iso_country);
        } else if (isset(self::getCountryData()[$iso_country.'-'.$iso_lang])) {
            return self::getInstanceByKey($iso_country.'-'.$iso_lang);
        } else {
            return self::getInstanceByKey('gb');
        }
    }

    /**
     * @param int
     * @return self
     */
    public static function getInstanceBySiteId($siteId)
    {
        foreach (self::getCountryData() as $data) {
            if ($data['site_id'] == $siteId) {
                try {
                    $country = new Country(Country::getByIso($data['iso_code']));
                } catch (Exception $e) {
                    return new self();
                }

                return new self($country);
            }
        }

        return new self();
    }

    /**
     * Set dev or not
     * @param boolean $dev set dev or not
     */
    public function setDev($dev)
    {
        if (is_bool($dev)) {
            $this->dev = $dev;
        }
    }

    /**
     * Get key for iso_code tab
     * @return string Key for iso_code tab
     */
    public static function getKeyForEbayCountry()
    {
        $country = new Country((int) Configuration::get('PS_COUNTRY_DEFAULT'));

        $default_country = Tools::strtolower($country->iso_code);

        if (in_array($default_country, EbayCountrySpec::$multilang)) {
            $lang = new Language((int) Configuration::get('PS_LANG_DEFAULT'));

            $default_country .= '-'.Tools::strtolower($lang->iso_code);
        }

        return $default_country;
    }

    public static function getSiteNameBySiteId($site_id = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['site_id'] == $site_id) {
                return $country['site_name'];
            }
        }
        return null;
    }

    public static function getSiteExtensionBySiteId($site_id = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['site_id'] == $site_id) {
                return $country['site_extension'];
            }
        }
        return null;
    }

    public static function getIsoCodeBySiteId($site_id = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['site_id'] == $site_id) {
                return $country['iso_code'];
            }
        }
        return null;
    }

    public static function getProUrlBySiteId($site_id = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['site_id'] == $site_id) {
                return $country['pro_url'];
            }
        }
        return null;
    }

    public static function getSiteIdByIsoCode($iso_code = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['iso_code'] == $iso_code) {
                return $country['site_id'];
            }
        }
        return null;
    }

    public static function getSiteIDBySiteNAme($site_name = false)
    {
        foreach (self::getCountryData() as $country) {
            if ($country['site_name'] == $site_name) {
                return $country['site_id'];
            }
        }

        return null;
    }
    
    protected static function getCountryData()
    {
        return EbaySiteMap::get();
    }
}
