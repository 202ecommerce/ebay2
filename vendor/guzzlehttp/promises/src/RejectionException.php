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
 * A special exception that is thrown when waiting on a rejected promise.
 *
 * The reason value is available via the getReason() method.
 */
class RejectionException extends \RuntimeException
{
    /** @var mixed Rejection reason. */
    private $reason;
    /**
     * @param mixed  $reason      Rejection reason.
     * @param string $description Optional description
     */
    public function __construct($reason, $description = null)
    {
        $this->reason = $reason;
        $message = 'The promise was rejected';
        if ($description) {
            $message .= ' with reason: ' . $description;
        } elseif (\is_string($reason) || \is_object($reason) && \method_exists($reason, '__toString')) {
            $message .= ' with reason: ' . $this->reason;
        } elseif ($reason instanceof \JsonSerializable) {
            $message .= ' with reason: ' . \json_encode($this->reason, \JSON_PRETTY_PRINT);
        }
        parent::__construct($message);
    }
    /**
     * Returns the rejection reason.
     *
     * @return mixed
     */
    public function getReason()
    {
        return $this->reason;
    }
}
