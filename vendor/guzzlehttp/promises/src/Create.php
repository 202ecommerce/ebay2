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

final class Create
{
    /**
     * Creates a promise for a value if the value is not a promise.
     *
     * @param mixed $value Promise or value.
     *
     * @return PromiseInterface
     */
    public static function promiseFor($value)
    {
        if ($value instanceof PromiseInterface) {
            return $value;
        }
        // Return a Guzzle promise that shadows the given promise.
        if (\is_object($value) && \method_exists($value, 'then')) {
            $wfn = \method_exists($value, 'wait') ? [$value, 'wait'] : null;
            $cfn = \method_exists($value, 'cancel') ? [$value, 'cancel'] : null;
            $promise = new Promise($wfn, $cfn);
            $value->then([$promise, 'resolve'], [$promise, 'reject']);
            return $promise;
        }
        return new FulfilledPromise($value);
    }
    /**
     * Creates a rejected promise for a reason if the reason is not a promise.
     * If the provided reason is a promise, then it is returned as-is.
     *
     * @param mixed $reason Promise or reason.
     *
     * @return PromiseInterface
     */
    public static function rejectionFor($reason)
    {
        if ($reason instanceof PromiseInterface) {
            return $reason;
        }
        return new RejectedPromise($reason);
    }
    /**
     * Create an exception for a rejected promise value.
     *
     * @param mixed $reason
     *
     * @return \Exception|\Throwable
     */
    public static function exceptionFor($reason)
    {
        if ($reason instanceof \Exception || $reason instanceof \Throwable) {
            return $reason;
        }
        return new RejectionException($reason);
    }
    /**
     * Returns an iterator for the given value.
     *
     * @param mixed $value
     *
     * @return \Iterator
     */
    public static function iterFor($value)
    {
        if ($value instanceof \Iterator) {
            return $value;
        }
        if (\is_array($value)) {
            return new \ArrayIterator($value);
        }
        return new \ArrayIterator([$value]);
    }
}
