<?php

/**
 * Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Ajax_Serializer
 *
 * NOTICE OF LICENSE
 * 
 * Faett_Package is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 * 
 * Faett_Package is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 * 
 * You should have received a copy of the GNU General Public License
 * along with Faett_Package.  If not, see <http://www.gnu.org/licenses/>.
 *
 * DISCLAIMER
 *
 * Do not edit or add to this file if you wish to upgrade Faett_Package to newer
 * versions in the future. If you wish to customize Faett_Package for your
 * needs please refer to http://www.faett.net for more information.
 *
 * @category   Faett
 * @package    Faett_Package
 * @copyright  Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    <http://www.gnu.org/licenses/> 
 * 			   GNU General Public License (GPL 3)
 */

/**
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Ajax_Serializer
    extends Mage_Core_Block_Template {

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Core/Block/Mage_Core_Block_Abstract#_construct()
     */
    public function _construct()
    {
        parent::_construct();
        $this->setTemplate('package/product/edit/serializer.phtml');
        return $this;
    }

    /**
     * Returns a JSON encoded JavaScript function call that refreshes
     * the grid with the product's maintainers.
     *
     * @return string The JSON encoded JavaScript function call
     */
    public function getMaintainersJSON()
    {
        // initialize the array with the JSON encoded string
        $result = array();
        if ($this->getMaintainers()) {
            foreach ($this->getMaintainers() as $maintainer) {
                $result[$maintainer->getUserId()] = $maintainer->toArray(array('active'));
            }
        }
        return $result ? Zend_Json_Encoder::encode($result) : '{}';
    }
}
