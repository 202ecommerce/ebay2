<?php


namespace Ebay\classes\SDK\Core;


use GuzzleHttp\RequestOptions;

abstract class AbstractBearerRequest implements RequestInterface
{
    /** @var BearerAuthToken*/
    protected $token;

    public function __construct(BearerAuthToken $token)
    {
        $this->token = $token;
    }

    public function getOptions()
    {
        return [
            RequestOptions::HEADERS => [
                'Authorization' => 'Bearer ' . $this->token->get()
            ]
        ];
    }
}