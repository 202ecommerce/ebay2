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
 * Provides a read only stream that pumps data from a PHP callable.
 *
 * When invoking the provided callable, the PumpStream will pass the amount of
 * data requested to read to the callable. The callable can choose to ignore
 * this value and return fewer or more bytes than requested. Any extra data
 * returned by the provided callable is buffered internally until drained using
 * the read() function of the PumpStream. The provided callable MUST return
 * false when there is no more data to read.
 *
 * @final
 */
class PumpStream implements StreamInterface
{
    /** @var callable */
    private $source;
    /** @var int */
    private $size;
    /** @var int */
    private $tellPos = 0;
    /** @var array */
    private $metadata;
    /** @var BufferStream */
    private $buffer;
    /**
     * @param callable $source  Source of the stream data. The callable MAY
     *                          accept an integer argument used to control the
     *                          amount of data to return. The callable MUST
     *                          return a string when called, or false on error
     *                          or EOF.
     * @param array    $options Stream options:
     *                          - metadata: Hash of metadata to use with stream.
     *                          - size: Size of the stream, if known.
     */
    public function __construct(callable $source, array $options = [])
    {
        $this->source = $source;
        $this->size = isset($options['size']) ? $options['size'] : null;
        $this->metadata = isset($options['metadata']) ? $options['metadata'] : [];
        $this->buffer = new BufferStream();
    }
    public function __toString()
    {
        try {
            return Utils::copyToString($this);
        } catch (\Exception $e) {
            return '';
        }
    }
    public function close()
    {
        $this->detach();
    }
    public function detach()
    {
        $this->tellPos = \false;
        $this->source = null;
        return null;
    }
    public function getSize()
    {
        return $this->size;
    }
    public function tell()
    {
        return $this->tellPos;
    }
    public function eof()
    {
        return !$this->source;
    }
    public function isSeekable()
    {
        return \false;
    }
    public function rewind()
    {
        $this->seek(0);
    }
    public function seek($offset, $whence = \SEEK_SET)
    {
        throw new \RuntimeException('Cannot seek a PumpStream');
    }
    public function isWritable()
    {
        return \false;
    }
    public function write($string)
    {
        throw new \RuntimeException('Cannot write to a PumpStream');
    }
    public function isReadable()
    {
        return \true;
    }
    public function read($length)
    {
        $data = $this->buffer->read($length);
        $readLen = \strlen($data);
        $this->tellPos += $readLen;
        $remaining = $length - $readLen;
        if ($remaining) {
            $this->pump($remaining);
            $data .= $this->buffer->read($remaining);
            $this->tellPos += \strlen($data) - $readLen;
        }
        return $data;
    }
    public function getContents()
    {
        $result = '';
        while (!$this->eof()) {
            $result .= $this->read(1000000);
        }
        return $result;
    }
    public function getMetadata($key = null)
    {
        if (!$key) {
            return $this->metadata;
        }
        return isset($this->metadata[$key]) ? $this->metadata[$key] : null;
    }
    private function pump($length)
    {
        if ($this->source) {
            do {
                $data = \call_user_func($this->source, $length);
                if ($data === \false || $data === null) {
                    $this->source = null;
                    return;
                }
                $this->buffer->write($data);
                $length -= \strlen($data);
            } while ($length > 0);
        }
    }
}
