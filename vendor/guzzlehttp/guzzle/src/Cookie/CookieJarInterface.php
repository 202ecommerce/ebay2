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

namespace EbayVendor\GuzzleHttp\Cookie;

use EbayVendor\Psr\Http\Message\RequestInterface;
use EbayVendor\Psr\Http\Message\ResponseInterface;
/**
 * Stores HTTP cookies.
 *
 * It extracts cookies from HTTP requests, and returns them in HTTP responses.
 * CookieJarInterface instances automatically expire contained cookies when
 * necessary. Subclasses are also responsible for storing and retrieving
 * cookies from a file, database, etc.
 *
 * @link http://docs.python.org/2/library/cookielib.html Inspiration
 */
interface CookieJarInterface extends \Countable, \IteratorAggregate
{
    /**
     * Create a request with added cookie headers.
     *
     * If no matching cookies are found in the cookie jar, then no Cookie
     * header is added to the request and the same request is returned.
     *
     * @param RequestInterface $request Request object to modify.
     *
     * @return RequestInterface returns the modified request.
     */
    public function withCookieHeader(RequestInterface $request);
    /**
     * Extract cookies from an HTTP response and store them in the CookieJar.
     *
     * @param RequestInterface  $request  Request that was sent
     * @param ResponseInterface $response Response that was received
     */
    public function extractCookies(RequestInterface $request, ResponseInterface $response);
    /**
     * Sets a cookie in the cookie jar.
     *
     * @param SetCookie $cookie Cookie to set.
     *
     * @return bool Returns true on success or false on failure
     */
    public function setCookie(SetCookie $cookie);
    /**
     * Remove cookies currently held in the cookie jar.
     *
     * Invoking this method without arguments will empty the whole cookie jar.
     * If given a $domain argument only cookies belonging to that domain will
     * be removed. If given a $domain and $path argument, cookies belonging to
     * the specified path within that domain are removed. If given all three
     * arguments, then the cookie with the specified name, path and domain is
     * removed.
     *
     * @param string|null $domain Clears cookies matching a domain
     * @param string|null $path   Clears cookies matching a domain and path
     * @param string|null $name   Clears cookies matching a domain, path, and name
     *
     * @return CookieJarInterface
     */
    public function clear($domain = null, $path = null, $name = null);
    /**
     * Discard all sessions cookies.
     *
     * Removes cookies that don't have an expire field or a have a discard
     * field set to true. To be called when the user agent shuts down according
     * to RFC 2965.
     */
    public function clearSessionCookies();
    /**
     * Converts the cookie jar to an array.
     *
     * @return array
     */
    public function toArray();
}
