<?php


namespace Ebay\classes\SDK\Account\CreateFulfilmentPolicy;

use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Request as GetRequest;
use Ebay\classes\SDK\Account\CreateFulfilmentPolicy\Response as GetResponse;
use Ebay\classes\SDK\Core\ApiBaseUri;
use Ebay\classes\SDK\Core\BearerAuthToken;
use Ebay\classes\SDK\Core\EbayApiResponse;
use Ebay\classes\SDK\Core\EbayClient;
use Ebay\classes\SDK\Lib\FulfilmentPolicy;
use Ebay\classes\SDK\Lib\FulfilmentPolicyList;
use Ebay\services\Builder\BuilderInterface;
use Ebay\services\Builder\FulfilmentBuilder;
use EbayVendor\GuzzleHttp\Exception\RequestException;
use Symfony\Component\VarDumper\VarDumper;
use Exception;

class CreateFulfilmentPolicy
{
    /** @var \EbayProfile*/
    protected $ebayProfile;

    /** @var mixed*/
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
                'response' => $e->getResponse()
            ];
            $body = $e->getResponse()->getBody()->getContents();
            $headers = $e->getResponse()->getHeaders();
            \Ebay::debug([
                $body
            ]);
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
                'response' => $result
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
        return (new BearerAuthToken($this->ebayProfile));
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