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

namespace Ebay\classes\SDK\Core;

use Configuration;
use Ebay\services\EbayContext;
use EbayVendor\GuzzleHttp\Client;
use EbayVendor\GuzzleHttp\RequestOptions;
use EbayVendor\Psr\Http\Message\ResponseInterface;

class EbayClient
{
    /** @var EbayVendor\GuzzleHttp\Client*/
    protected $client;

    /** @var ApiBaseUriInterface*/
    protected $apiBaseUri;

    protected $ebayContext;

    public function __construct(ApiBaseUriInterface $apiBaseUri)
    {
        $this->client = new Client(['base_uri' => $apiBaseUri->get()]);
        $this->apiBaseUri = $apiBaseUri;
        $this->ebayContext = EbayContext::getContext();
    }

    public function executeRequest(RequestInterface $request)
    {
        try {
            $result = $this->client->request(
                $request->getMethod(),
                $request->getEndPoint(),
                array_merge($this->getOptions(), $request->getOptions())
            );

            if ($this->isLoggingActive()) {
                $this->log([
                    'request' => $request,
                    'response' => $result
                ]);
            }
        } catch (\Exception $e) {
            if ($this->isLoggingActive()) {
                $this->log([
                    'request' => $request,
                    'error' => $e,
                ]);
            }

            throw $e;
        } catch (\Throwable $e) {// Throwable is available from php 7
            if ($this->isLoggingActive()) {
                $this->log([
                    'request' => $request,
                    'error' => $e,
                ]);
            }

            throw $e;
        }


        return $result;
    }

    /** @return array*/
    protected function getOptions()
    {
        return [];
    }

    protected function isLoggingActive()
    {
        return (int)Configuration::get('EBAY_API_LOGS');
    }

    protected function log($params)
    {
        $ebayApiLog = new \EbayApiLog();

        if (empty($params['request'])) {
            return;
        }

        if ($params['request'] instanceof RequestInterface == false) {
            return;
        }

        $ebayApiLog->type = get_class($params['request']);
        $ebayApiLog->request = $params['request']->toJson();
        $ebayApiLog->id_ebay_profile = $this->ebayContext->get('id_ebay_profile_sync_process', 0);

        if (false == empty($params['error'])) {
            $ebayApiLog->status = 'KO';

            if (is_callable([$params['error'], 'getMessage'])) {
                $ebayApiLog->response = $params['error']->getMessage();
            }

            $ebayApiLog->save();
            return;
        }

        if (empty($params['response'])) {
            return;
        }

        if ($params['response'] instanceof ResponseInterface == false) {
            return;
        }

        $ebayApiLog->status = 'OK';
        $ebayApiLog->response = json_encode($params['response']->getBody()->getContents());
        $ebayApiLog->save();
        $params['response']->getBody()->rewind();
    }
}
