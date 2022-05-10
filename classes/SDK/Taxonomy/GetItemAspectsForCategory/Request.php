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

namespace Ebay\classes\SDK\Taxonomy\GetItemAspectsForCategory;

use Ebay\classes\SDK\Core\AbstractBearerRequest;
use Ebay\classes\SDK\Core\BearerAuthToken;

class Request extends AbstractBearerRequest
{
    /** @var string */
    protected $categoryTreeId;

    /** @var string */
    protected $categoryId;

    public function __construct(BearerAuthToken $token, $categoryTreeId, $categoryId)
    {
        parent::__construct($token);

        $this->categoryTreeId = (string) $categoryTreeId;
        $this->categoryId = (string) $categoryId;
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
    public function getMethod()
    {
        return 'get';
    }
}
