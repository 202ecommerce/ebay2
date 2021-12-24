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
 * Uses PHP's zlib.inflate filter to inflate deflate or gzipped content.
 *
 * This stream decorator skips the first 10 bytes of the given stream to remove
 * the gzip header, converts the provided stream to a PHP stream resource,
 * then appends the zlib.inflate filter. The stream is then converted back
 * to a Guzzle stream resource to be used as a Guzzle stream.
 *
 * @link http://tools.ietf.org/html/rfc1952
 * @link http://php.net/manual/en/filters.compression.php
 *
 * @final
 */
class InflateStream implements StreamInterface
{
    use StreamDecoratorTrait;
    public function __construct(StreamInterface $stream)
    {
        // read the first 10 bytes, ie. gzip header
        $header = $stream->read(10);
        $filenameHeaderLength = $this->getLengthOfPossibleFilenameHeader($stream, $header);
        // Skip the header, that is 10 + length of filename + 1 (nil) bytes
        $stream = new LimitStream($stream, -1, 10 + $filenameHeaderLength);
        $resource = StreamWrapper::getResource($stream);
        \stream_filter_append($resource, 'zlib.inflate', \STREAM_FILTER_READ);
        $this->stream = $stream->isSeekable() ? new Stream($resource) : new NoSeekStream(new Stream($resource));
    }
    /**
     * @param StreamInterface $stream
     * @param $header
     *
     * @return int
     */
    private function getLengthOfPossibleFilenameHeader(StreamInterface $stream, $header)
    {
        $filename_header_length = 0;
        if (\substr(\bin2hex($header), 6, 2) === '08') {
            // we have a filename, read until nil
            $filename_header_length = 1;
            while ($stream->read(1) !== \chr(0)) {
                $filename_header_length++;
            }
        }
        return $filename_header_length;
    }
}
