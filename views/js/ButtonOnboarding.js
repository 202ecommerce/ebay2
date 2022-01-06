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

    this.modal.querySelector('[save-onboarding-url-btn]').addEventListener('click', this.saveOnboardingUrl.bind(this));

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

    var onboardingUrl = this.getOnboardingUrl();

    if (false == onboardingUrl) {
        $(this.modal).modal('show');
        return;
    }

    window.location.href = onboardingUrl;
};

ButtonOnboarding.prototype.saveOnboardingUrl = function () {
    if (this.controller == null) {
        return;
    }

    if (this.modal == null) {
        return;
    }

    var onboardingUrl = this.getOnboardingUrl();

    if (false == onboardingUrl) {
        return;
    }

    var controllerUrl = new URL(this.controller);
    controllerUrl.searchParams.append('ajax', 1);
    controllerUrl.searchParams.append('action', 'SaveOnboardingUrl');
    fetch(
        controllerUrl.toString(),
        {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({onboardingUrl: onboardingUrl})
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