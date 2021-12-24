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
 * Stream decorator that can cache previously read bytes from a sequentially
 * read stream.
 *
 * @final
 */
class CachingStream implements StreamInterface
{
    use StreamDecoratorTrait;
    /** @var StreamInterface Stream being wrapped */
    private $remoteStream;
    /** @var int Number of bytes to skip reading due to a write on the buffer */
    private $skipReadBytes = 0;
    /**
     * We will treat the buffer object as the body of the stream
     *
     * @param StreamInterface $stream Stream to cache. The cursor is assumed to be at the beginning of the stream.
     * @param StreamInterface $target Optionally specify where data is cached
     */
    public function __construct(StreamInterface $stream, StreamInterface $target = null)
    {
        $this->remoteStream = $stream;
        $this->stream = $target ?: new Stream(Utils::tryFopen('php://temp', 'r+'));
    }
    public function getSize()
    {
        return \max($this->stream->getSize(), $this->remoteStream->getSize());
    }
    public function rewind()
    {
        $this->seek(0);
    }
    public function seek($offset, $whence = \SEEK_SET)
    {
        if ($whence == \SEEK_SET) {
            $byte = $offset;
        } elseif ($whence == \SEEK_CUR) {
            $byte = $offset + $this->tell();
        } elseif ($whence == \SEEK_END) {
            $size = $this->remoteStream->getSize();
            if ($size === null) {
                $size = $this->cacheEntireStream();
            }
            $byte = $size + $offset;
        } else {
            throw new \InvalidArgumentException('Invalid whence');
        }
        $diff = $byte - $this->stream->getSize();
        if ($diff > 0) {
            // Read the remoteStream until we have read in at least the amount
            // of bytes requested, or we reach the end of the file.
            while ($diff > 0 && !$this->remoteStream->eof()) {
                $this->read($diff);
                $diff = $byte - $this->stream->getSize();
            }
        } else {
            // We can just do a normal seek since we've already seen this byte.
            $this->stream->seek($byte);
        }
    }
    public function read($length)
    {
        // Perform a regular read on any previously read data from the buffer
        $data = $this->stream->read($length);
        $remaining = $length - \strlen($data);
        // More data was requested so read from the remote stream
        if ($remaining) {
            // If data was written to the buffer in a position that would have
            // been filled from the remote stream, then we must skip bytes on
            // the remote stream to emulate overwriting bytes from that
            // position. This mimics the behavior of other PHP stream wrappers.
            $remoteData = $this->remoteStream->read($remaining + $this->skipReadBytes);
            if ($this->skipReadBytes) {
                $len = \strlen($remoteData);
                $remoteData = \substr($remoteData, $this->skipReadBytes);
                $this->skipReadBytes = \max(0, $this->skipReadBytes - $len);
            }
            $data .= $remoteData;
            $this->stream->write($remoteData);
        }
        return $data;
    }
    public function write($string)
    {
        // When appending to the end of the currently read stream, you'll want
        // to skip bytes from being read from the remote stream to emulate
        // other stream wrappers. Basically replacing bytes of data of a fixed
        // length.
        $overflow = \strlen($string) + $this->tell() - $this->remoteStream->tell();
        if ($overflow > 0) {
            $this->skipReadBytes += $overflow;
        }
        return $this->stream->write($string);
    }
    public function eof()
    {
        return $this->stream->eof() && $this->remoteStream->eof();
    }
    /**
     * Close both the remote stream and buffer stream
     */
    public function close()
    {
        $this->remoteStream->close() && $this->stream->close();
    }
    private function cacheEntireStream()
    {
        $target = new FnStream(['write' => 'strlen']);
        Utils::copyToStream($this, $target);
        return $this->tell();
    }
}
