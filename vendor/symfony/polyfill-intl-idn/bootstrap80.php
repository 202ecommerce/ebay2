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
use EbayVendor\Symfony\Polyfill\Intl\Idn as p;
if (!\defined('U_IDNA_PROHIBITED_ERROR')) {
    \define('U_IDNA_PROHIBITED_ERROR', 66560);
}
if (!\defined('U_IDNA_ERROR_START')) {
    \define('U_IDNA_ERROR_START', 66560);
}
if (!\defined('U_IDNA_UNASSIGNED_ERROR')) {
    \define('U_IDNA_UNASSIGNED_ERROR', 66561);
}
if (!\defined('U_IDNA_CHECK_BIDI_ERROR')) {
    \define('U_IDNA_CHECK_BIDI_ERROR', 66562);
}
if (!\defined('U_IDNA_STD3_ASCII_RULES_ERROR')) {
    \define('U_IDNA_STD3_ASCII_RULES_ERROR', 66563);
}
if (!\defined('U_IDNA_ACE_PREFIX_ERROR')) {
    \define('U_IDNA_ACE_PREFIX_ERROR', 66564);
}
if (!\defined('U_IDNA_VERIFICATION_ERROR')) {
    \define('U_IDNA_VERIFICATION_ERROR', 66565);
}
if (!\defined('U_IDNA_LABEL_TOO_LONG_ERROR')) {
    \define('U_IDNA_LABEL_TOO_LONG_ERROR', 66566);
}
if (!\defined('U_IDNA_ZERO_LENGTH_LABEL_ERROR')) {
    \define('U_IDNA_ZERO_LENGTH_LABEL_ERROR', 66567);
}
if (!\defined('U_IDNA_DOMAIN_NAME_TOO_LONG_ERROR')) {
    \define('U_IDNA_DOMAIN_NAME_TOO_LONG_ERROR', 66568);
}
if (!\defined('U_IDNA_ERROR_LIMIT')) {
    \define('U_IDNA_ERROR_LIMIT', 66569);
}
if (!\defined('U_STRINGPREP_PROHIBITED_ERROR')) {
    \define('U_STRINGPREP_PROHIBITED_ERROR', 66560);
}
if (!\defined('U_STRINGPREP_UNASSIGNED_ERROR')) {
    \define('U_STRINGPREP_UNASSIGNED_ERROR', 66561);
}
if (!\defined('U_STRINGPREP_CHECK_BIDI_ERROR')) {
    \define('U_STRINGPREP_CHECK_BIDI_ERROR', 66562);
}
if (!\defined('IDNA_DEFAULT')) {
    \define('IDNA_DEFAULT', 0);
}
if (!\defined('IDNA_ALLOW_UNASSIGNED')) {
    \define('IDNA_ALLOW_UNASSIGNED', 1);
}
if (!\defined('IDNA_USE_STD3_RULES')) {
    \define('IDNA_USE_STD3_RULES', 2);
}
if (!\defined('IDNA_CHECK_BIDI')) {
    \define('IDNA_CHECK_BIDI', 4);
}
if (!\defined('IDNA_CHECK_CONTEXTJ')) {
    \define('IDNA_CHECK_CONTEXTJ', 8);
}
if (!\defined('IDNA_NONTRANSITIONAL_TO_ASCII')) {
    \define('IDNA_NONTRANSITIONAL_TO_ASCII', 16);
}
if (!\defined('IDNA_NONTRANSITIONAL_TO_UNICODE')) {
    \define('IDNA_NONTRANSITIONAL_TO_UNICODE', 32);
}
if (!\defined('INTL_IDNA_VARIANT_UTS46')) {
    \define('INTL_IDNA_VARIANT_UTS46', 1);
}
if (!\defined('IDNA_ERROR_EMPTY_LABEL')) {
    \define('IDNA_ERROR_EMPTY_LABEL', 1);
}
if (!\defined('IDNA_ERROR_LABEL_TOO_LONG')) {
    \define('IDNA_ERROR_LABEL_TOO_LONG', 2);
}
if (!\defined('IDNA_ERROR_DOMAIN_NAME_TOO_LONG')) {
    \define('IDNA_ERROR_DOMAIN_NAME_TOO_LONG', 4);
}
if (!\defined('IDNA_ERROR_LEADING_HYPHEN')) {
    \define('IDNA_ERROR_LEADING_HYPHEN', 8);
}
if (!\defined('IDNA_ERROR_TRAILING_HYPHEN')) {
    \define('IDNA_ERROR_TRAILING_HYPHEN', 16);
}
if (!\defined('IDNA_ERROR_HYPHEN_3_4')) {
    \define('IDNA_ERROR_HYPHEN_3_4', 32);
}
if (!\defined('IDNA_ERROR_LEADING_COMBINING_MARK')) {
    \define('IDNA_ERROR_LEADING_COMBINING_MARK', 64);
}
if (!\defined('IDNA_ERROR_DISALLOWED')) {
    \define('IDNA_ERROR_DISALLOWED', 128);
}
if (!\defined('IDNA_ERROR_PUNYCODE')) {
    \define('IDNA_ERROR_PUNYCODE', 256);
}
if (!\defined('IDNA_ERROR_LABEL_HAS_DOT')) {
    \define('IDNA_ERROR_LABEL_HAS_DOT', 512);
}
if (!\defined('IDNA_ERROR_INVALID_ACE_LABEL')) {
    \define('IDNA_ERROR_INVALID_ACE_LABEL', 1024);
}
if (!\defined('IDNA_ERROR_BIDI')) {
    \define('IDNA_ERROR_BIDI', 2048);
}
if (!\defined('IDNA_ERROR_CONTEXTJ')) {
    \define('IDNA_ERROR_CONTEXTJ', 4096);
}
if (!\function_exists('idn_to_ascii')) {
    function idn_to_ascii(?string $domain, ?int $flags = 0, ?int $variant = \INTL_IDNA_VARIANT_UTS46, &$idna_info = null) : string|false
    {
        return p\Idn::idn_to_ascii((string) $domain, (int) $flags, (int) $variant, $idna_info);
    }
}
if (!\function_exists('idn_to_utf8')) {
    function idn_to_utf8(?string $domain, ?int $flags = 0, ?int $variant = \INTL_IDNA_VARIANT_UTS46, &$idna_info = null) : string|false
    {
        return p\Idn::idn_to_utf8((string) $domain, (int) $flags, (int) $variant, $idna_info);
    }
}
