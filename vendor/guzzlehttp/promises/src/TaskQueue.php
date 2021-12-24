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
 * A task queue that executes tasks in a FIFO order.
 *
 * This task queue class is used to settle promises asynchronously and
 * maintains a constant stack size. You can use the task queue asynchronously
 * by calling the `run()` function of the global task queue in an event loop.
 *
 *     GuzzleHttp\Promise\Utils::queue()->run();
 */
class TaskQueue implements TaskQueueInterface
{
    private $enableShutdown = \true;
    private $queue = [];
    public function __construct($withShutdown = \true)
    {
        if ($withShutdown) {
            \register_shutdown_function(function () {
                if ($this->enableShutdown) {
                    // Only run the tasks if an E_ERROR didn't occur.
                    $err = \error_get_last();
                    if (!$err || $err['type'] ^ \E_ERROR) {
                        $this->run();
                    }
                }
            });
        }
    }
    public function isEmpty()
    {
        return !$this->queue;
    }
    public function add(callable $task)
    {
        $this->queue[] = $task;
    }
    public function run()
    {
        while ($task = \array_shift($this->queue)) {
            /** @var callable $task */
            $task();
        }
    }
    /**
     * The task queue will be run and exhausted by default when the process
     * exits IFF the exit is not the result of a PHP E_ERROR error.
     *
     * You can disable running the automatic shutdown of the queue by calling
     * this function. If you disable the task queue shutdown process, then you
     * MUST either run the task queue (as a result of running your event loop
     * or manually using the run() method) or wait on each outstanding promise.
     *
     * Note: This shutdown will occur before any destructors are triggered.
     */
    public function disableShutdown()
    {
        $this->enableShutdown = \false;
    }
}
