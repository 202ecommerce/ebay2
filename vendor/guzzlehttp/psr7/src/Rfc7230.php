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

namespace EbayVendor\GuzzleHttp\Psr7;

final class Rfc7230
{
    /**
     * Header related regular expressions (copied from amphp/http package)
     * (Note: once we require PHP 7.x we could just depend on the upstream package)
     *
     * Note: header delimiter (\r\n) is modified to \r?\n to accept line feed only delimiters for BC reasons.
     *
     * @link    https://github.com/amphp/http/blob/v1.0.1/src/Rfc7230.php#L12-L15
     *
     * @license https://github.com/amphp/http/blob/v1.0.1/LICENSE
     */
    const HEADER_REGEX = "(^([^()<>@,;:\\\"/[\\]?={}\1- ]++):[ \t]*+((?:[ \t]*+[!-~�-�]++)*+)[ \t]*+\r?\n)m";
    const HEADER_FOLD_REGEX = "(\r?\n[ \t]++)";
}
