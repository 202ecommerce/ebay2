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
 *
 */

namespace EbayVendor;

/*
 * This file is part of the Symfony package.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
use EbayVendor\Symfony\Polyfill\Intl\Normalizer as p;
if (\PHP_VERSION_ID >= 80000) {
    return require __DIR__ . '/bootstrap80.php';
}
if (!\function_exists('normalizer_is_normalized')) {
    function normalizer_is_normalized($string, $form = p\Normalizer::FORM_C)
    {
        return p\Normalizer::isNormalized($string, $form);
    }
}
if (!\function_exists('normalizer_normalize')) {
    function normalizer_normalize($string, $form = p\Normalizer::FORM_C)
    {
        return p\Normalizer::normalize($string, $form);
    }
}
