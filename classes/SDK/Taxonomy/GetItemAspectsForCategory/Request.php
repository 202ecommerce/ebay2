<?php


namespace Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory;


use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;

class Request extends AbstractBearerRequest
{
    /** @var string*/
    protected $categoryTreeId;

    /** @var string*/
    protected $categoryId;

    public function __construct(BearerAuthToken $token, $categoryTreeId, $categoryId)
    {
        parent::__construct($token);

        $this->categoryTreeId = (string)$categoryTreeId;
        $this->categoryId = (string)$categoryId;
    }

    /** @return string */
    public function getEndPoint()
    {
        return sprintf(
            '/commerce/taxonomy/v1/category_tree/%s/get_item_aspects_for_category?category_id=%s',
            $this->categoryTreeId,
            $this->categoryId
        );
    }

    /** @return string */
    public function toJson()
    {
        return '';
    }

    /** @return array */
    public function toArray()
    {
        return [];
    }

    /** @return string */
    public function getMethod()
    {
        return 'get';
    }
}