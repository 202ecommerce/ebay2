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

namespace Ebay\classes\SDK\Account\UpdateFulfilmentPolicy;

use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\CreateFulfilmentPolicy;
use Ebay\classes\SDK\Account\UpdateFulfilmentPolicy\Request as UpdateRequest;
use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Response as GetResponse;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;
use Ebay\services\Builder\BuilderInterface;
use Ebay\services\Builder\FulfilmentBuilder;
use Symfony\Component\VarDumper\VarDumper;
use Exception;

class UpdateFulfilmentPolicy extends CreateFulfilmentPolicy
{
    protected function getRequest()
    {
        $fulfilment = $this->getFulfilmentBuilder()->build();
        return new UpdateRequest(
            $this->getToken(),
            $fulfilment
        );
    }
}
