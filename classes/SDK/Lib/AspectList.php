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

namespace Ebay\classes\SDK\Lib;

use Ebay\classes\SDK\Core\ResourceModel;
use Symfony\Component\VarDumper\VarDumper;

class AspectList extends ResourceModel
{
    /** @var Aspect[]*/
    protected $aspectList = [];

    /**
     * @param array $data
     * @return self
     */
    public function fromArray($data)
    {
        if (empty($data['aspects'])) {
            return $this;
        }

        if (false == is_array($data['aspects'])) {
            return $this;
        }

        foreach ($data['aspects'] as $row) {
            $this->add(
                (new Aspect())->fromArray($row)
            );
        }

        return $this;
    }

    public function toArray()
    {
        $output = [];

        if (empty($this->getList())) {
            return $output;
        }

        foreach ($this->getList() as $aspect) {
            $output[] = $aspect->toArray();
        }

        return $output;
    }

    /**
     * @return Aspect[]
     */
    public function getList()
    {
        return $this->aspectList;
    }

    /**
     * @param Aspect $aspect
     * @return self
     */
    public function add(Aspect $aspect)
    {
        $this->aspectList[] = $aspect;
        return $this;
    }

    public function set($aspects)
    {
        $this->aspectList = [];

        foreach ($aspects as $aspect) {
            if ($aspect instanceof Aspect) {
                $this->add($aspect);
            }
        }

        return $this;
    }
}
