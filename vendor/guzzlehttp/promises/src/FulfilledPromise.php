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

namespace EbayVendor\GuzzleHttp\Promise;

/**
 * A promise that has been fulfilled.
 *
 * Thenning off of this promise will invoke the onFulfilled callback
 * immediately and ignore other callbacks.
 */
class FulfilledPromise implements PromiseInterface
{
    private $value;
    public function __construct($value)
    {
        if (\is_object($value) && \method_exists($value, 'then')) {
            throw new \InvalidArgumentException('You cannot create a FulfilledPromise with a promise.');
        }
        $this->value = $value;
    }
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        // Return itself if there is no onFulfilled function.
        if (!$onFulfilled) {
            return $this;
        }
        $queue = Utils::queue();
        $p = new Promise([$queue, 'run']);
        $value = $this->value;
        $queue->add(static function () use($p, $value, $onFulfilled) {
            if (Is::pending($p)) {
                try {
                    $p->resolve($onFulfilled($value));
                } catch (\Throwable $e) {
                    $p->reject($e);
                } catch (\Exception $e) {
                    $p->reject($e);
                }
            }
        });
        return $p;
    }
    public function otherwise(callable $onRejected)
    {
        return $this->then(null, $onRejected);
    }
    public function wait($unwrap = \true, $defaultDelivery = null)
    {
        return $unwrap ? $this->value : null;
    }
    public function getState()
    {
        return self::FULFILLED;
    }
    public function resolve($value)
    {
        if ($value !== $this->value) {
            throw new \LogicException("Cannot resolve a fulfilled promise");
        }
    }
    public function reject($reason)
    {
        throw new \LogicException("Cannot reject a fulfilled promise");
    }
    public function cancel()
    {
        // pass
    }
}
