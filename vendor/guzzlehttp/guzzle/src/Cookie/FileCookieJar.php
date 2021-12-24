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

/**
 * Persists non-session cookies using a JSON formatted file
 */
class FileCookieJar extends CookieJar
{
    /** @var string filename */
    private $filename;
    /** @var bool Control whether to persist session cookies or not. */
    private $storeSessionCookies;
    /**
     * Create a new FileCookieJar object
     *
     * @param string $cookieFile        File to store the cookie data
     * @param bool $storeSessionCookies Set to true to store session cookies
     *                                  in the cookie jar.
     *
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function __construct($cookieFile, $storeSessionCookies = \false)
    {
        parent::__construct();
        $this->filename = $cookieFile;
        $this->storeSessionCookies = $storeSessionCookies;
        if (\file_exists($cookieFile)) {
            $this->load($cookieFile);
        }
    }
    /**
     * Saves the file when shutting down
     */
    public function __destruct()
    {
        $this->save($this->filename);
    }
    /**
     * Saves the cookies to a file.
     *
     * @param string $filename File to save
     * @throws \RuntimeException if the file cannot be found or created
     */
    public function save($filename)
    {
        $json = [];
        foreach ($this as $cookie) {
            /** @var SetCookie $cookie */
            if (CookieJar::shouldPersist($cookie, $this->storeSessionCookies)) {
                $json[] = $cookie->toArray();
            }
        }
        $jsonStr = \EbayVendor\GuzzleHttp\json_encode($json);
        if (\false === \file_put_contents($filename, $jsonStr, \LOCK_EX)) {
            throw new \RuntimeException("Unable to save file {$filename}");
        }
    }
    /**
     * Load cookies from a JSON formatted file.
     *
     * Old cookies are kept unless overwritten by newly loaded ones.
     *
     * @param string $filename Cookie file to load.
     * @throws \RuntimeException if the file cannot be loaded.
     */
    public function load($filename)
    {
        $json = \file_get_contents($filename);
        if (\false === $json) {
            throw new \RuntimeException("Unable to load file {$filename}");
        } elseif ($json === '') {
            return;
        }
        $data = \EbayVendor\GuzzleHttp\json_decode($json, \true);
        if (\is_array($data)) {
            foreach (\json_decode($json, \true) as $cookie) {
                $this->setCookie(new SetCookie($cookie));
            }
        } elseif (\strlen($data)) {
            throw new \RuntimeException("Invalid cookie file: {$filename}");
        }
    }
}
