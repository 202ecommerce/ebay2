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
if (version_compare(_PS_VERSION_, '1.7', '>=')) {
    /**
     * Class allow to display tpl on the FO
     */
    class BWDisplay extends FrontController
    {
        // Assign template, on 1.4 create it else assign for 1.5
        public function setTemplate($template, $params = [], $locale = null)
        {
            if (_PS_VERSION_ >= '1.5') {
                parent::setTemplate($template, $params, $locale);
            } else {
                $this->template = $template;
            }
        }

        // Overload displayContent for 1.4
        public function displayContent()
        {
            parent::displayContent();

            echo Context::getContext()->smarty->fetch($this->template);
        }
    }
} else {
    /**
     * Class allow to display tpl on the FO
     */
    class BWDisplay extends FrontController
    {
        // Assign template, on 1.4 create it else assign for 1.5
        public function setTemplate($template)
        {
            if (_PS_VERSION_ >= '1.5') {
                parent::setTemplate($template);
            } else {
                $this->template = $template;
            }
        }

        // Overload displayContent for 1.4
        public function displayContent()
        {
            parent::displayContent();

            echo Context::getContext()->smarty->fetch($this->template);
        }
    }
}
