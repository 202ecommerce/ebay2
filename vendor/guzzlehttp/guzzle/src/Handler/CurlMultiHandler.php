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

use EbayVendor\GuzzleHttp\Promise as P;
use EbayVendor\GuzzleHttp\Promise\Promise;
use EbayVendor\GuzzleHttp\Utils;
use EbayVendor\Psr\Http\Message\RequestInterface;
/**
 * Returns an asynchronous response using curl_multi_* functions.
 *
 * When using the CurlMultiHandler, custom curl options can be specified as an
 * associative array of curl option constants mapping to values in the
 * **curl** key of the provided request options.
 *
 * @property resource $_mh Internal use only. Lazy loaded multi-handle.
 */
class CurlMultiHandler
{
    /** @var CurlFactoryInterface */
    private $factory;
    private $selectTimeout;
    private $active;
    private $handles = [];
    private $delays = [];
    private $options = [];
    /**
     * This handler accepts the following options:
     *
     * - handle_factory: An optional factory  used to create curl handles
     * - select_timeout: Optional timeout (in seconds) to block before timing
     *   out while selecting curl handles. Defaults to 1 second.
     * - options: An associative array of CURLMOPT_* options and
     *   corresponding values for curl_multi_setopt()
     *
     * @param array $options
     */
    public function __construct(array $options = [])
    {
        $this->factory = isset($options['handle_factory']) ? $options['handle_factory'] : new CurlFactory(50);
        if (isset($options['select_timeout'])) {
            $this->selectTimeout = $options['select_timeout'];
        } elseif ($selectTimeout = \getenv('GUZZLE_CURL_SELECT_TIMEOUT')) {
            $this->selectTimeout = $selectTimeout;
        } else {
            $this->selectTimeout = 1;
        }
        $this->options = isset($options['options']) ? $options['options'] : [];
    }
    public function __get($name)
    {
        if ($name === '_mh') {
            $this->_mh = \curl_multi_init();
            foreach ($this->options as $option => $value) {
                // A warning is raised in case of a wrong option.
                \curl_multi_setopt($this->_mh, $option, $value);
            }
            // Further calls to _mh will return the value directly, without entering the
            // __get() method at all.
            return $this->_mh;
        }
        throw new \BadMethodCallException();
    }
    public function __destruct()
    {
        if (isset($this->_mh)) {
            \curl_multi_close($this->_mh);
            unset($this->_mh);
        }
    }
    public function __invoke(RequestInterface $request, array $options)
    {
        $easy = $this->factory->create($request, $options);
        $id = (int) $easy->handle;
        $promise = new Promise([$this, 'execute'], function () use($id) {
            return $this->cancel($id);
        });
        $this->addRequest(['easy' => $easy, 'deferred' => $promise]);
        return $promise;
    }
    /**
     * Ticks the curl event loop.
     */
    public function tick()
    {
        // Add any delayed handles if needed.
        if ($this->delays) {
            $currentTime = Utils::currentTime();
            foreach ($this->delays as $id => $delay) {
                if ($currentTime >= $delay) {
                    unset($this->delays[$id]);
                    \curl_multi_add_handle($this->_mh, $this->handles[$id]['easy']->handle);
                }
            }
        }
        // Step through the task queue which may add additional requests.
        P\queue()->run();
        if ($this->active && \curl_multi_select($this->_mh, $this->selectTimeout) === -1) {
            // Perform a usleep if a select returns -1.
            // See: https://bugs.php.net/bug.php?id=61141
            \usleep(250);
        }
        while (\curl_multi_exec($this->_mh, $this->active) === \CURLM_CALL_MULTI_PERFORM) {
        }
        $this->processMessages();
    }
    /**
     * Runs until all outstanding connections have completed.
     */
    public function execute()
    {
        $queue = P\queue();
        while ($this->handles || !$queue->isEmpty()) {
            // If there are no transfers, then sleep for the next delay
            if (!$this->active && $this->delays) {
                \usleep($this->timeToNext());
            }
            $this->tick();
        }
    }
    private function addRequest(array $entry)
    {
        $easy = $entry['easy'];
        $id = (int) $easy->handle;
        $this->handles[$id] = $entry;
        if (empty($easy->options['delay'])) {
            \curl_multi_add_handle($this->_mh, $easy->handle);
        } else {
            $this->delays[$id] = Utils::currentTime() + $easy->options['delay'] / 1000;
        }
    }
    /**
     * Cancels a handle from sending and removes references to it.
     *
     * @param int $id Handle ID to cancel and remove.
     *
     * @return bool True on success, false on failure.
     */
    private function cancel($id)
    {
        // Cannot cancel if it has been processed.
        if (!isset($this->handles[$id])) {
            return \false;
        }
        $handle = $this->handles[$id]['easy']->handle;
        unset($this->delays[$id], $this->handles[$id]);
        \curl_multi_remove_handle($this->_mh, $handle);
        \curl_close($handle);
        return \true;
    }
    private function processMessages()
    {
        while ($done = \curl_multi_info_read($this->_mh)) {
            $id = (int) $done['handle'];
            \curl_multi_remove_handle($this->_mh, $done['handle']);
            if (!isset($this->handles[$id])) {
                // Probably was cancelled.
                continue;
            }
            $entry = $this->handles[$id];
            unset($this->handles[$id], $this->delays[$id]);
            $entry['easy']->errno = $done['result'];
            $entry['deferred']->resolve(CurlFactory::finish($this, $entry['easy'], $this->factory));
        }
    }
    private function timeToNext()
    {
        $currentTime = Utils::currentTime();
        $nextTime = \PHP_INT_MAX;
        foreach ($this->delays as $time) {
            if ($time < $nextTime) {
                $nextTime = $time;
            }
        }
        return \max(0, $nextTime - $currentTime) * 1000000;
    }
}
