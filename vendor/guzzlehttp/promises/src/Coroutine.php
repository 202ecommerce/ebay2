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

use Exception;
use Generator;
use Throwable;
/**
 * Creates a promise that is resolved using a generator that yields values or
 * promises (somewhat similar to C#'s async keyword).
 *
 * When called, the Coroutine::of method will start an instance of the generator
 * and returns a promise that is fulfilled with its final yielded value.
 *
 * Control is returned back to the generator when the yielded promise settles.
 * This can lead to less verbose code when doing lots of sequential async calls
 * with minimal processing in between.
 *
 *     use GuzzleHttp\Promise;
 *
 *     function createPromise($value) {
 *         return new Promise\FulfilledPromise($value);
 *     }
 *
 *     $promise = Promise\Coroutine::of(function () {
 *         $value = (yield createPromise('a'));
 *         try {
 *             $value = (yield createPromise($value . 'b'));
 *         } catch (\Exception $e) {
 *             // The promise was rejected.
 *         }
 *         yield $value . 'c';
 *     });
 *
 *     // Outputs "abc"
 *     $promise->then(function ($v) { echo $v; });
 *
 * @param callable $generatorFn Generator function to wrap into a promise.
 *
 * @return Promise
 *
 * @link https://github.com/petkaantonov/bluebird/blob/master/API.md#generators inspiration
 */
final class Coroutine implements PromiseInterface
{
    /**
     * @var PromiseInterface|null
     */
    private $currentPromise;
    /**
     * @var Generator
     */
    private $generator;
    /**
     * @var Promise
     */
    private $result;
    public function __construct(callable $generatorFn)
    {
        $this->generator = $generatorFn();
        $this->result = new Promise(function () {
            while (isset($this->currentPromise)) {
                $this->currentPromise->wait();
            }
        });
        try {
            $this->nextCoroutine($this->generator->current());
        } catch (\Exception $exception) {
            $this->result->reject($exception);
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
    /**
     * Create a new coroutine.
     *
     * @return self
     */
    public static function of(callable $generatorFn)
    {
        return new self($generatorFn);
    }
    public function then(callable $onFulfilled = null, callable $onRejected = null)
    {
        return $this->result->then($onFulfilled, $onRejected);
    }
    public function otherwise(callable $onRejected)
    {
        return $this->result->otherwise($onRejected);
    }
    public function wait($unwrap = \true)
    {
        return $this->result->wait($unwrap);
    }
    public function getState()
    {
        return $this->result->getState();
    }
    public function resolve($value)
    {
        $this->result->resolve($value);
    }
    public function reject($reason)
    {
        $this->result->reject($reason);
    }
    public function cancel()
    {
        $this->currentPromise->cancel();
        $this->result->cancel();
    }
    private function nextCoroutine($yielded)
    {
        $this->currentPromise = Create::promiseFor($yielded)->then([$this, '_handleSuccess'], [$this, '_handleFailure']);
    }
    /**
     * @internal
     */
    public function _handleSuccess($value)
    {
        unset($this->currentPromise);
        try {
            $next = $this->generator->send($value);
            if ($this->generator->valid()) {
                $this->nextCoroutine($next);
            } else {
                $this->result->resolve($value);
            }
        } catch (Exception $exception) {
            $this->result->reject($exception);
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
    /**
     * @internal
     */
    public function _handleFailure($reason)
    {
        unset($this->currentPromise);
        try {
            $nextYield = $this->generator->throw(Create::exceptionFor($reason));
            // The throw was caught, so keep iterating on the coroutine
            $this->nextCoroutine($nextYield);
        } catch (Exception $exception) {
            $this->result->reject($exception);
        } catch (Throwable $throwable) {
            $this->result->reject($throwable);
        }
    }
}
