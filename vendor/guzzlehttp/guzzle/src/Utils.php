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

namespace EbayVendor\GuzzleHttp;

use EbayVendor\GuzzleHttp\Exception\InvalidArgumentException;
use EbayVendor\Psr\Http\Message\UriInterface;
use EbayVendor\Symfony\Polyfill\Intl\Idn\Idn;
final class Utils
{
    /**
     * Wrapper for the hrtime() or microtime() functions
     * (depending on the PHP version, one of the two is used)
     *
     * @return float|mixed UNIX timestamp
     *
     * @internal
     */
    public static function currentTime()
    {
        return \function_exists('hrtime') ? \hrtime(\true) / 1000000000.0 : \microtime(\true);
    }
    /**
     * @param int $options
     *
     * @return UriInterface
     * @throws InvalidArgumentException
     *
     * @internal
     */
    public static function idnUriConvert(UriInterface $uri, $options = 0)
    {
        if ($uri->getHost()) {
            $asciiHost = self::idnToAsci($uri->getHost(), $options, $info);
            if ($asciiHost === \false) {
                $errorBitSet = isset($info['errors']) ? $info['errors'] : 0;
                $errorConstants = \array_filter(\array_keys(\get_defined_constants()), function ($name) {
                    return \substr($name, 0, 11) === 'IDNA_ERROR_';
                });
                $errors = [];
                foreach ($errorConstants as $errorConstant) {
                    if ($errorBitSet & \constant($errorConstant)) {
                        $errors[] = $errorConstant;
                    }
                }
                $errorMessage = 'IDN conversion failed';
                if ($errors) {
                    $errorMessage .= ' (errors: ' . \implode(', ', $errors) . ')';
                }
                throw new InvalidArgumentException($errorMessage);
            } else {
                if ($uri->getHost() !== $asciiHost) {
                    // Replace URI only if the ASCII version is different
                    $uri = $uri->withHost($asciiHost);
                }
            }
        }
        return $uri;
    }
    /**
     * @param string $domain
     * @param int    $options
     * @param array  $info
     *
     * @return string|false
     */
    private static function idnToAsci($domain, $options, &$info = [])
    {
        if (\preg_match('%^[ -~]+$%', $domain) === 1) {
            return $domain;
        }
        if (\extension_loaded('intl') && \defined('INTL_IDNA_VARIANT_UTS46')) {
            return \idn_to_ascii($domain, $options, \INTL_IDNA_VARIANT_UTS46, $info);
        }
        /*
         * The Idn class is marked as @internal. Verify that class and method exists.
         */
        if (\method_exists(Idn::class, 'idn_to_ascii')) {
            return Idn::idn_to_ascii($domain, $options, Idn::INTL_IDNA_VARIANT_UTS46, $info);
        }
        throw new \RuntimeException('ext-intl or symfony/polyfill-intl-idn not loaded or too old');
    }
}
