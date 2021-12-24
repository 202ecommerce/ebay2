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
 * Stream decorator trait
 *
 * @property StreamInterface stream
 */
trait StreamDecoratorTrait
{
    /**
     * @param StreamInterface $stream Stream to decorate
     */
    public function __construct(StreamInterface $stream)
    {
        $this->stream = $stream;
    }
    /**
     * Magic method used to create a new stream if streams are not added in
     * the constructor of a decorator (e.g., LazyOpenStream).
     *
     * @param string $name Name of the property (allows "stream" only).
     *
     * @return StreamInterface
     */
    public function __get($name)
    {
        if ($name == 'stream') {
            $this->stream = $this->createStream();
            return $this->stream;
        }
        throw new \UnexpectedValueException("{$name} not found on class");
    }
    public function __toString()
    {
        try {
            if ($this->isSeekable()) {
                $this->seek(0);
            }
            return $this->getContents();
        } catch (\Exception $e) {
            // Really, PHP? https://bugs.php.net/bug.php?id=53648
            \trigger_error('StreamDecorator::__toString exception: ' . (string) $e, \E_USER_ERROR);
            return '';
        }
    }
    public function getContents()
    {
        return Utils::copyToString($this);
    }
    /**
     * Allow decorators to implement custom methods
     *
     * @param string $method Missing method name
     * @param array  $args   Method arguments
     *
     * @return mixed
     */
    public function __call($method, array $args)
    {
        $result = \call_user_func_array([$this->stream, $method], $args);
        // Always return the wrapped object if the result is a return $this
        return $result === $this->stream ? $this : $result;
    }
    public function close()
    {
        $this->stream->close();
    }
    public function getMetadata($key = null)
    {
        return $this->stream->getMetadata($key);
    }
    public function detach()
    {
        return $this->stream->detach();
    }
    public function getSize()
    {
        return $this->stream->getSize();
    }
    public function eof()
    {
        return $this->stream->eof();
    }
    public function tell()
    {
        return $this->stream->tell();
    }
    public function isReadable()
    {
        return $this->stream->isReadable();
    }
    public function isWritable()
    {
        return $this->stream->isWritable();
    }
    public function isSeekable()
    {
        return $this->stream->isSeekable();
    }
    public function rewind()
    {
        $this->seek(0);
    }
    public function seek($offset, $whence = \SEEK_SET)
    {
        $this->stream->seek($offset, $whence);
    }
    public function read($length)
    {
        return $this->stream->read($length);
    }
    public function write($string)
    {
        return $this->stream->write($string);
    }
    /**
     * Implement in subclasses to dynamically create streams when requested.
     *
     * @return StreamInterface
     *
     * @throws \BadMethodCallException
     */
    protected function createStream()
    {
        throw new \BadMethodCallException('Not implemented');
    }
}
