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
 * Converts Guzzle streams into PHP stream resources.
 *
 * @final
 */
class StreamWrapper
{
    /** @var resource */
    public $context;
    /** @var StreamInterface */
    private $stream;
    /** @var string r, r+, or w */
    private $mode;
    /**
     * Returns a resource representing the stream.
     *
     * @param StreamInterface $stream The stream to get a resource for
     *
     * @return resource
     *
     * @throws \InvalidArgumentException if stream is not readable or writable
     */
    public static function getResource(StreamInterface $stream)
    {
        self::register();
        if ($stream->isReadable()) {
            $mode = $stream->isWritable() ? 'r+' : 'r';
        } elseif ($stream->isWritable()) {
            $mode = 'w';
        } else {
            throw new \InvalidArgumentException('The stream must be readable, ' . 'writable, or both.');
        }
        return \fopen('guzzle://stream', $mode, null, self::createStreamContext($stream));
    }
    /**
     * Creates a stream context that can be used to open a stream as a php stream resource.
     *
     * @param StreamInterface $stream
     *
     * @return resource
     */
    public static function createStreamContext(StreamInterface $stream)
    {
        return \stream_context_create(['guzzle' => ['stream' => $stream]]);
    }
    /**
     * Registers the stream wrapper if needed
     */
    public static function register()
    {
        if (!\in_array('guzzle', \stream_get_wrappers())) {
            \stream_wrapper_register('guzzle', __CLASS__);
        }
    }
    public function stream_open($path, $mode, $options, &$opened_path)
    {
        $options = \stream_context_get_options($this->context);
        if (!isset($options['guzzle']['stream'])) {
            return \false;
        }
        $this->mode = $mode;
        $this->stream = $options['guzzle']['stream'];
        return \true;
    }
    public function stream_read($count)
    {
        return $this->stream->read($count);
    }
    public function stream_write($data)
    {
        return (int) $this->stream->write($data);
    }
    public function stream_tell()
    {
        return $this->stream->tell();
    }
    public function stream_eof()
    {
        return $this->stream->eof();
    }
    public function stream_seek($offset, $whence)
    {
        $this->stream->seek($offset, $whence);
        return \true;
    }
    public function stream_cast($cast_as)
    {
        $stream = clone $this->stream;
        return $stream->detach();
    }
    public function stream_stat()
    {
        static $modeMap = ['r' => 33060, 'rb' => 33060, 'r+' => 33206, 'w' => 33188, 'wb' => 33188];
        return ['dev' => 0, 'ino' => 0, 'mode' => $modeMap[$this->mode], 'nlink' => 0, 'uid' => 0, 'gid' => 0, 'rdev' => 0, 'size' => $this->stream->getSize() ?: 0, 'atime' => 0, 'mtime' => 0, 'ctime' => 0, 'blksize' => 0, 'blocks' => 0];
    }
    public function url_stat($path, $flags)
    {
        return ['dev' => 0, 'ino' => 0, 'mode' => 0, 'nlink' => 0, 'uid' => 0, 'gid' => 0, 'rdev' => 0, 'size' => 0, 'atime' => 0, 'mtime' => 0, 'ctime' => 0, 'blksize' => 0, 'blocks' => 0];
    }
}
