/*
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


var ButtonOnboarding = function(conf) {
    if (conf['btn'] instanceof Element) {
        this.button = conf['btn'];
    } else {
        this.button = null;
    }

    if (conf['confBtn'] instanceof Element) {
        this.confBtn = conf['confBtn'];
    } else {
        this.confBtn = null;
    }

    if (conf['modal'] instanceof Element) {
        this.modal = conf['modal'];
    } else {
        this.modal = null;
    }

    if (typeof conf['controller'] != 'undefined') {
        this.controller = conf['controller'];
    } else {
        this.controller = null;
    }
};

ButtonOnboarding.prototype.init = function () {
    if (this.button == null) {
        return;
    }

    this.button.addEventListener('click', this.handleClick.bind(this));
    this.confBtn.addEventListener('click', function () {
        $(this.modal).modal('show');
    }.bind(this));

    if (this.modal == null) {
        return;
    }

    this.modal.querySelector('[save-onboarding-url-btn]').addEventListener('click', this.saveOnboardingConf.bind(this));

    if (false == this.getOnboardingUrl()) {
        this.button.disabled = true;
    }
};

ButtonOnboarding.prototype.getOnboardingUrl = function () {
    if (this.modal == null) {
        return '';
    }

    try {
        return this.modal.querySelector('[name="EBAY_ONBOARDING_URL"]').value;
    } catch (e) {
        return '';
    }
};

ButtonOnboarding.prototype.handleClick = function (e) {
    if (this.modal == null) {
        return;
    }

    if (this.isConfigured() == false) {
        $(this.modal).modal('show');
        return;
    }

    window.location.href = this.getOnboardingUrl();
};

ButtonOnboarding.prototype.isConfigured = function () {
    if (false == this.getOnboardingUrl()) {
        return false;
    }

    if (false == this.getAppId()) {
        return false;
    }

    if (false == this.getCertId()) {
        return false;
    }

    if (false == this.getRuName()) {
        return false;
    }

    return  true;
};

ButtonOnboarding.prototype.getAppId = function () {
    if (this.modal == null) {
        return '';
    }

    try {
        return this.modal.querySelector('[name="EBAY_APP_ID"]').value;
    } catch (e) {
        return '';
    }
};

ButtonOnboarding.prototype.getCertId = function () {
    if (this.modal == null) {
        return '';
    }

    try {
        return this.modal.querySelector('[name="EBAY_CERT_ID"]').value;
    } catch (e) {
        return '';
    }
};

ButtonOnboarding.prototype.getRuName = function () {
    if (this.modal == null) {
        return '';
    }

    try {
        return this.modal.querySelector('[name="EBAY_RU_NAME"]').value;
    } catch (e) {
        return '';
    }
};

ButtonOnboarding.prototype.saveOnboardingConf = function () {
    if (this.controller == null) {
        return;
    }

    if (this.modal == null) {
        return;
    }

    if (this.isConfigured() == false) {
        return;
    }

    var data = {
        onboardingUrl: this.getOnboardingUrl(),
        appId: this.getAppId(),
        certId: this.getCertId(),
        ruName: this.getRuName()
    };

    var controllerUrl = new URL(this.controller);
    controllerUrl.searchParams.append('ajax', 1);
    controllerUrl.searchParams.append('action', 'SaveOnboardingConf');
    fetch(
        controllerUrl.toString(),
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify(data)
        }
    ).then(function(res) {
        return res.json();
    }).then(function(data) {
        if (data.success) {
            this.button.disabled = false;
            $(this.modal).modal('hide');
        }
    }.bind(this));
};