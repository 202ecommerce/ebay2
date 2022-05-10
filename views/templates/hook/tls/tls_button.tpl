{*
* 2007-2022 PrestaShop
*
* NOTICE OF LICENSE
*
* This source file is subject to the Academic Free License (AFL 3.0)
* that is bundled with this package in the file LICENSE.txt.
* It is also available through the world-wide-web at this URL:
* http://opensource.org/licenses/afl-3.0.php
* If you did not receive a copy of the license and are unable to
* obtain it through the world-wide-web, please send an email
* to license@prestashop.com so we can send you a copy immediately.
*
* DISCLAIMER
*
* Do not edit or add to this file if you wish to upgrade PrestaShop to newer
* versions in the future. If you wish to customize PrestaShop for your
* needs please refer to http://www.prestashop.com for more information.
*
* @author 202-ecommerce <tech@202-ecommerce.com>
* @copyright Copyright (c) 2007-2022 202-ecommerce
* @license Commercial license
*  International Registered Trademark & Property of PrestaShop SA
*}

<div checking-tls-section>
    <button class="btn btn-default" check-tls-btn>
        {l s='Check TLS' mod='ebay'}
    </button>
    <div tls-checking-response></div>
</div>

<script>
    document.querySelector('[check-tls-btn]').addEventListener('click', function(event) {
        var url = new URL('{$controller}');
        url.searchParams.append('ajax', 1);
        url.searchParams.append('action', 'CheckTls');

        fetch(url.toString(), {
            method: 'get',
            headers: {
                'content-type': 'application/json;charset=utf-8'
            },
        })
            .then(function (response) {
                return response.json();
            })
            .then(function (response) {
                document.querySelector('[tls-checking-response]').outerHTML = response.message;
            });
    })
</script>



