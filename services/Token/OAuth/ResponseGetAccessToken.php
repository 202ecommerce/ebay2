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

namespace Ebay\services\Token\OAuth;


class ResponseGetAccessToken
{
    /** @var bool*/
    protected $success;

    /** @var string*/
    protected $error;

    /** @var string*/
    protected $accessToken;

    /** @var string*/
    protected $refreshToken;

    /**
     * @return bool
     */
    public function isSuccess()
    {
        return (bool)$this->success;
    }

    /**
     * @param bool $success
     * @return self
     */
    public function setSuccess($success)
    {
        $this->success = (bool)$success;
        return $this;
    }

    /**
     * @return string
     */
    public function getError()
    {
        return (string)$this->error;
    }

    /**
     * @param string $error
     * @return self
     */
    public function setError($error)
    {
        $this->error = (string)$error;
        return $this;
    }

    /**
     * @return string
     */
    public function getAccessToken()
    {
        return (string)$this->accessToken;
    }

    /**
     * @param string $accessToken
     * @return self
     */
    public function setAccessToken($accessToken)
    {
        $this->accessToken = (string)$accessToken;
        return $this;
    }

    /**
     * @return string
     */
    public function getRefreshToken()
    {
        return (string)$this->refreshToken;
    }

    /**
     * @param string $refreshToken
     * @return self
     */
    public function setRefreshToken($refreshToken)
    {
        $this->refreshToken = (string)$refreshToken;
        return $this;
    }
}