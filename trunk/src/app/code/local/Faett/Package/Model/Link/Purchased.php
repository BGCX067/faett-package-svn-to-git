<?php

/**
 * Faett_Package_Model_Link_Purchased
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
 * Package links purchased model.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Link_Purchased extends Mage_Core_Model_Abstract
{
    const XML_PATH_ORDER_STATE = 'catalog/package/order_state';

    const LINK_STATE_PENDING   = 'pending';
    const LINK_STATE_AVAILABLE = 'available';
    const LINK_STATE_EXPIRED   = 'expired';
    const LINK_STATE_PENDING_PAYMENT = 'pending_payment';

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('package/link_purchased');
        parent::_construct();
    }

    public function getOrderItem()
    {
         return Mage::getModel('sales/order_item')->load($this->getOrderItemId());
    }

    public function getProduct()
    {
        return Mage::getModel('catalog/product')->load($this->getProductId());
    }

    public function getLink()
    {
        return Mage::getModel('package/link')->load($this->getLinkId());
    }
}