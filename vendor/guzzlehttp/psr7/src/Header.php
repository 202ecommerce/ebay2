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

final class Header
{
    /**
     * Parse an array of header values containing ";" separated data into an
     * array of associative arrays representing the header key value pair data
     * of the header. When a parameter does not contain a value, but just
     * contains a key, this function will inject a key with a '' string value.
     *
     * @param string|array $header Header to parse into components.
     *
     * @return array Returns the parsed header values.
     */
    public static function parse($header)
    {
        static $trimmed = "\"'  \n\t\r";
        $params = $matches = [];
        foreach (self::normalize($header) as $val) {
            $part = [];
            foreach (\preg_split('/;(?=([^"]*"[^"]*")*[^"]*$)/', $val) as $kvp) {
                if (\preg_match_all('/<[^>]+>|[^=]+/', $kvp, $matches)) {
                    $m = $matches[0];
                    if (isset($m[1])) {
                        $part[\trim($m[0], $trimmed)] = \trim($m[1], $trimmed);
                    } else {
                        $part[] = \trim($m[0], $trimmed);
                    }
                }
            }
            if ($part) {
                $params[] = $part;
            }
        }
        return $params;
    }
    /**
     * Converts an array of header values that may contain comma separated
     * headers into an array of headers with no comma separated values.
     *
     * @param string|array $header Header to normalize.
     *
     * @return array Returns the normalized header field values.
     */
    public static function normalize($header)
    {
        if (!\is_array($header)) {
            return \array_map('trim', \explode(',', $header));
        }
        $result = [];
        foreach ($header as $value) {
            foreach ((array) $value as $v) {
                if (\strpos($v, ',') === \false) {
                    $result[] = $v;
                    continue;
                }
                foreach (\preg_split('/,(?=([^"]*"[^"]*")*[^"]*$)/', $v) as $vv) {
                    $result[] = \trim($vv);
                }
            }
        }
        return $result;
    }
}
