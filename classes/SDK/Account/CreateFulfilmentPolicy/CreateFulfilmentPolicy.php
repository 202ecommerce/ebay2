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
 */

namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Request as GetRequest;
use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Response as GetResponse;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\services\Builder\BuilderInterface;
use Ebay\services\Builder\FulfilmentBuilder;
use EbayVendor\GuzzleHttp\Exception\RequestException;
use Exception;

class CreateFulfilmentPolicy
{
    /** @var \EbayProfile */
    protected $ebayProfile;

    /** @var mixed */
    protected $data;

    public function __construct(\EbayProfile $ebayProfile, $data)
    {
        $this->ebayProfile = $ebayProfile;
        $this->setData($data);
    }

    /**
     * @return GetResponse
     */
    public function execute()
    {
        try {
            $result = $this->getClient()->executeRequest($this->getRequest());
        } catch (RequestException $e) {
            $result = [
                'response' => $e->getResponse(),
            ];
            $body = $e->getResponse()->getBody()->getContents();
            $headers = $e->getResponse()->getHeaders();

            if (false == empty($headers['content-type'])) {
                if (in_array('application/json', explode(';', $headers['content-type'][0]))) {
                    $bodyArray = json_decode(
                        $body,
                        true
                    );

                    $result['body-response'] = $bodyArray;
                }
            }

            return (new GetResponse())->setSuccess(false)->setResult($result);
        } catch (Exception $e) {
            return (new GetResponse())->setSuccess(false);
        }

        if ($result->getStatusCode() < 200 || $result->getStatusCode() > 300) {
            $res = [
                'response' => $result,
            ];
            $body = $result->getBody()->getContents();
            $headers = $result->getHeaders();

            if (false == empty($headers['content-type'])) {
                if (in_array('application/json', explode(';', $headers['content-type'][0]))) {
                    $bodyArray = json_decode(
                        $body,
                        true
                    );

                    $res['body-response'] = $bodyArray;
                }
            }

            return (new GetResponse())
                ->setSuccess(false)
                ->setResult($res);
        }

        $fulfilmentPolicy = (new FulfilmentPolicy())->fromJson($result->getBody()->getContents());
        $response = (new GetResponse())
            ->setSuccess(true)
            ->setResult($result)
            ->setFulfilmentPolicy($fulfilmentPolicy);

        return $response;
    }

    protected function getToken()
    {
        return new BearerAuthToken($this->ebayProfile);
    }

    /**
     * @return EbayClient
     */
    protected function getClient()
    {
        return new EbayClient($this->getApiBaseUri());
    }

    protected function getApiBaseUri()
    {
        return new ApiBaseUri();
    }

    protected function getRequest()
    {
        $fulfilment = $this->getFulfilmentBuilder()->build();

        return new GetRequest(
            $this->getToken(),
            $fulfilment
        );
    }

    /**
     * @param mixed $data
     *
     * @return self
     */
    public function setData($data)
    {
        $this->data = $data;

        return $this;
    }

    /** @return BuilderInterface*/
    protected function getFulfilmentBuilder()
    {
        return new FulfilmentBuilder($this->data);
    }
}
