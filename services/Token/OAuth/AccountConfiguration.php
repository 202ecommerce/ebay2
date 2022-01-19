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


class AccountConfiguration
{
    /** @var string*/
    protected $appId;

    /** @var string*/
    protected $certId;

    /** @var string*/
    protected $ruName;

    /** @var bool*/
    protected $sandbox;

    /**
     * @return string
     */
    public function getAppId()
    {
        return (string)$this->appId;
    }

    /**
     * @param string $appId
     * @return self
     */
    public function setAppId($appId)
    {
        $this->appId = (string)$appId;
        return $this;
    }

    /**
     * @return string
     */
    public function getCertId()
    {
        return (string)$this->certId;
    }

    /**
     * @param string $certId
     * @return self
     */
    public function setCertId($certId)
    {
        $this->certId = (string)$certId;
        return $this;
    }

    /**
     * @return string
     */
    public function getRuName()
    {
        return (string)$this->ruName;
    }

    /**
     * @param string $ruName
     * @return self
     */
    public function setRuName($ruName)
    {
        $this->ruName = (string)$ruName;
        return $this;
    }

    /**
     * @return bool
     */
    public function isSandbox()
    {
        return (bool)$this->sandbox;
    }

    /**
     * @param bool $sandboxMode
     * @return self
     */
    public function setSandbox($sandboxMode)
    {
        $this->sandbox = (bool)$sandboxMode;
        return $this;
    }
}