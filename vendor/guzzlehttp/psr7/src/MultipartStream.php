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

use EbayVendor\Psr\Http\Message\StreamInterface;
/**
 * Stream that when read returns bytes for a streaming multipart or
 * multipart/form-data stream.
 *
 * @final
 */
class MultipartStream implements StreamInterface
{
    use StreamDecoratorTrait;
    private $boundary;
    /**
     * @param array  $elements Array of associative arrays, each containing a
     *                         required "name" key mapping to the form field,
     *                         name, a required "contents" key mapping to a
     *                         StreamInterface/resource/string, an optional
     *                         "headers" associative array of custom headers,
     *                         and an optional "filename" key mapping to a
     *                         string to send as the filename in the part.
     * @param string $boundary You can optionally provide a specific boundary
     *
     * @throws \InvalidArgumentException
     */
    public function __construct(array $elements = [], $boundary = null)
    {
        $this->boundary = $boundary ?: \sha1(\uniqid('', \true));
        $this->stream = $this->createStream($elements);
    }
    /**
     * Get the boundary
     *
     * @return string
     */
    public function getBoundary()
    {
        return $this->boundary;
    }
    public function isWritable()
    {
        return \false;
    }
    /**
     * Get the headers needed before transferring the content of a POST file
     */
    private function getHeaders(array $headers)
    {
        $str = '';
        foreach ($headers as $key => $value) {
            $str .= "{$key}: {$value}\r\n";
        }
        return "--{$this->boundary}\r\n" . \trim($str) . "\r\n\r\n";
    }
    /**
     * Create the aggregate stream that will be used to upload the POST data
     */
    protected function createStream(array $elements)
    {
        $stream = new AppendStream();
        foreach ($elements as $element) {
            $this->addElement($stream, $element);
        }
        // Add the trailing boundary with CRLF
        $stream->addStream(Utils::streamFor("--{$this->boundary}--\r\n"));
        return $stream;
    }
    private function addElement(AppendStream $stream, array $element)
    {
        foreach (['contents', 'name'] as $key) {
            if (!\array_key_exists($key, $element)) {
                throw new \InvalidArgumentException("A '{$key}' key is required");
            }
        }
        $element['contents'] = Utils::streamFor($element['contents']);
        if (empty($element['filename'])) {
            $uri = $element['contents']->getMetadata('uri');
            if (\substr($uri, 0, 6) !== 'php://') {
                $element['filename'] = $uri;
            }
        }
        list($body, $headers) = $this->createElement($element['name'], $element['contents'], isset($element['filename']) ? $element['filename'] : null, isset($element['headers']) ? $element['headers'] : []);
        $stream->addStream(Utils::streamFor($this->getHeaders($headers)));
        $stream->addStream($body);
        $stream->addStream(Utils::streamFor("\r\n"));
    }
    /**
     * @return array
     */
    private function createElement($name, StreamInterface $stream, $filename, array $headers)
    {
        // Set a default content-disposition header if one was no provided
        $disposition = $this->getHeader($headers, 'content-disposition');
        if (!$disposition) {
            $headers['Content-Disposition'] = $filename === '0' || $filename ? \sprintf('form-data; name="%s"; filename="%s"', $name, \basename($filename)) : "form-data; name=\"{$name}\"";
        }
        // Set a default content-length header if one was no provided
        $length = $this->getHeader($headers, 'content-length');
        if (!$length) {
            if ($length = $stream->getSize()) {
                $headers['Content-Length'] = (string) $length;
            }
        }
        // Set a default Content-Type if one was not supplied
        $type = $this->getHeader($headers, 'content-type');
        if (!$type && ($filename === '0' || $filename)) {
            if ($type = MimeType::fromFilename($filename)) {
                $headers['Content-Type'] = $type;
            }
        }
        return [$stream, $headers];
    }
    private function getHeader(array $headers, $key)
    {
        $lowercaseHeader = \strtolower($key);
        foreach ($headers as $k => $v) {
            if (\strtolower($k) === $lowercaseHeader) {
                return $v;
            }
        }
        return null;
    }
}
