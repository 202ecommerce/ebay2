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
 * Decorator used to return only a subset of a stream.
 *
 * @final
 */
class LimitStream implements StreamInterface
{
    use StreamDecoratorTrait;
    /** @var int Offset to start reading from */
    private $offset;
    /** @var int Limit the number of bytes that can be read */
    private $limit;
    /**
     * @param StreamInterface $stream Stream to wrap
     * @param int             $limit  Total number of bytes to allow to be read
     *                                from the stream. Pass -1 for no limit.
     * @param int             $offset Position to seek to before reading (only
     *                                works on seekable streams).
     */
    public function __construct(StreamInterface $stream, $limit = -1, $offset = 0)
    {
        $this->stream = $stream;
        $this->setLimit($limit);
        $this->setOffset($offset);
    }
    public function eof()
    {
        // Always return true if the underlying stream is EOF
        if ($this->stream->eof()) {
            return \true;
        }
        // No limit and the underlying stream is not at EOF
        if ($this->limit == -1) {
            return \false;
        }
        return $this->stream->tell() >= $this->offset + $this->limit;
    }
    /**
     * Returns the size of the limited subset of data
     * {@inheritdoc}
     */
    public function getSize()
    {
        if (null === ($length = $this->stream->getSize())) {
            return null;
        } elseif ($this->limit == -1) {
            return $length - $this->offset;
        } else {
            return \min($this->limit, $length - $this->offset);
        }
    }
    /**
     * Allow for a bounded seek on the read limited stream
     * {@inheritdoc}
     */
    public function seek($offset, $whence = \SEEK_SET)
    {
        if ($whence !== \SEEK_SET || $offset < 0) {
            throw new \RuntimeException(\sprintf('Cannot seek to offset %s with whence %s', $offset, $whence));
        }
        $offset += $this->offset;
        if ($this->limit !== -1) {
            if ($offset > $this->offset + $this->limit) {
                $offset = $this->offset + $this->limit;
            }
        }
        $this->stream->seek($offset);
    }
    /**
     * Give a relative tell()
     * {@inheritdoc}
     */
    public function tell()
    {
        return $this->stream->tell() - $this->offset;
    }
    /**
     * Set the offset to start limiting from
     *
     * @param int $offset Offset to seek to and begin byte limiting from
     *
     * @throws \RuntimeException if the stream cannot be seeked.
     */
    public function setOffset($offset)
    {
        $current = $this->stream->tell();
        if ($current !== $offset) {
            // If the stream cannot seek to the offset position, then read to it
            if ($this->stream->isSeekable()) {
                $this->stream->seek($offset);
            } elseif ($current > $offset) {
                throw new \RuntimeException("Could not seek to stream offset {$offset}");
            } else {
                $this->stream->read($offset - $current);
            }
        }
        $this->offset = $offset;
    }
    /**
     * Set the limit of bytes that the decorator allows to be read from the
     * stream.
     *
     * @param int $limit Number of bytes to allow to be read from the stream.
     *                   Use -1 for no limit.
     */
    public function setLimit($limit)
    {
        $this->limit = $limit;
    }
    public function read($length)
    {
        if ($this->limit == -1) {
            return $this->stream->read($length);
        }
        // Check if the current position is less than the total allowed
        // bytes + original offset
        $remaining = $this->offset + $this->limit - $this->stream->tell();
        if ($remaining > 0) {
            // Only return the amount of requested data, ensuring that the byte
            // limit is not exceeded
            return $this->stream->read(\min($remaining, $length));
        }
        return '';
    }
}
