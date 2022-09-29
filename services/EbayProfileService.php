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
class EbayProfileService
{
    /**
     * @param string $name
     * @param string $value
     * @param array $ids_profile
     * @param bool $html
     */
    public function setConfiguration($name, $value, $ids_profile, $html = false)
    {
        if (is_array($ids_profile) == false || empty($ids_profile)) {
            return false;
        }

        $collection = new PrestaShopCollection('EbayProfile');
        $collection->where('id_ebay_profile', 'in', $ids_profile);
        $profiles = $collection->getResults();
        foreach ($profiles as $profile) {
            /* @var $profile EbayProfile*/
            $profile->setConfiguration($name, $value, $html);
        }
    }
}
