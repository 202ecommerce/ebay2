<?php
namespace Ebay\classes\SDK\Core;

use EbayVendor\GuzzleHttp\Client;
use EbayVendor\GuzzleHttp\RequestOptions;

class EbayClient
{
    /** @var EbayVendor\GuzzleHttp\Client*/
    protected $client;

    /** @var ApiBaseUriInterface*/
    protected $apiBaseUri;

    public function __construct(ApiBaseUriInterface $apiBaseUri)
    {
        $this->client = new Client(['base_uri' => $apiBaseUri->get()]);
        $this->apiBaseUri = $apiBaseUri;
    }

    public function executeRequest(RequestInterface $request)
    {
        return $this->client->request(
            $request->getMethod(),
            $request->getEndPoint(),
            array_merge($this->getOptions(), $request->getOptions())
        );
    }

    /** @return array*/
    protected function getOptions()
    {
        return [];
    }
}