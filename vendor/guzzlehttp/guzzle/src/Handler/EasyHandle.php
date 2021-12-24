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

namespace EbayVendor\GuzzleHttp\Handler;

use EbayVendor\GuzzleHttp\Psr7\Response;
use EbayVendor\Psr\Http\Message\RequestInterface;
use EbayVendor\Psr\Http\Message\ResponseInterface;
use EbayVendor\Psr\Http\Message\StreamInterface;
/**
 * Represents a cURL easy handle and the data it populates.
 *
 * @internal
 */
final class EasyHandle
{
    /** @var resource cURL resource */
    public $handle;
    /** @var StreamInterface Where data is being written */
    public $sink;
    /** @var array Received HTTP headers so far */
    public $headers = [];
    /** @var ResponseInterface Received response (if any) */
    public $response;
    /** @var RequestInterface Request being sent */
    public $request;
    /** @var array Request options */
    public $options = [];
    /** @var int cURL error number (if any) */
    public $errno = 0;
    /** @var \Exception Exception during on_headers (if any) */
    public $onHeadersException;
    /**
     * Attach a response to the easy handle based on the received headers.
     *
     * @throws \RuntimeException if no headers have been received.
     */
    public function createResponse()
    {
        if (empty($this->headers)) {
            throw new \RuntimeException('No headers have been received');
        }
        // HTTP-version SP status-code SP reason-phrase
        $startLine = \explode(' ', \array_shift($this->headers), 3);
        $headers = \EbayVendor\GuzzleHttp\headers_from_lines($this->headers);
        $normalizedKeys = \EbayVendor\GuzzleHttp\normalize_header_keys($headers);
        if (!empty($this->options['decode_content']) && isset($normalizedKeys['content-encoding'])) {
            $headers['x-encoded-content-encoding'] = $headers[$normalizedKeys['content-encoding']];
            unset($headers[$normalizedKeys['content-encoding']]);
            if (isset($normalizedKeys['content-length'])) {
                $headers['x-encoded-content-length'] = $headers[$normalizedKeys['content-length']];
                $bodyLength = (int) $this->sink->getSize();
                if ($bodyLength) {
                    $headers[$normalizedKeys['content-length']] = $bodyLength;
                } else {
                    unset($headers[$normalizedKeys['content-length']]);
                }
            }
        }
        // Attach a response to the easy handle with the parsed headers.
        $this->response = new Response($startLine[1], $headers, $this->sink, \substr($startLine[0], 5), isset($startLine[2]) ? (string) $startLine[2] : null);
    }
    public function __get($name)
    {
        $msg = $name === 'handle' ? 'The EasyHandle has been released' : 'Invalid property: ' . $name;
        throw new \BadMethodCallException($msg);
    }
}
