<?php

/**
 * Faett_Package_Block_Customer_Products_List
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
 * Block to display package links bought by customer.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Block_Customer_Products_List
    extends Mage_Core_Block_Template {

    /**
     * Class constructor
     */
    public function __construct()
    {
        parent::__construct();
        // load the session for the actual customer
        $session = Mage::getSingleton('customer/session');
        // load the customers purchases
        $purchased = Mage::getResourceModel('package/link_purchased_collection')
            ->addFieldToFilter('customer_id', $session->getCustomerId())
            ->addOrder('created_at', 'desc');
        // set the purchases
        $this->setItems($purchased);
    }

    /**
     * Enter description here...
     *
     * @return Faett_Package_Block_Customer_Products_List
     */
    protected function _prepareLayout()
    {
        parent::_prepareLayout();
        // initialize the layout
        $pager = $this->getLayout()
            ->createBlock('page/html_pager', 'package.customer.products.pager')
            ->setCollection($this->getItems());
        $this->setChild('pager', $pager);
        $this->getItems()->load();
        return $this;
    }

    /**
     * Return order view url
     *
     * @param integer $orderId
     * @return string
     */
    public function getOrderViewUrl($orderId)
    {
        return $this->getUrl('sales/order/view', array('order_id' => $orderId));
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getBackUrl()
    {
        if ($this->getRefererUrl()) {
            return $this->getRefererUrl();
        }
        return $this->getUrl('customer/account/');
    }

    /**
     * Return url to download link
     *
     * @param Faett_Package_Model_Link_Purchased $linkPurchased
     * @return string
     */
    public function getDownloadUrl(
        Faett_Package_Model_Link_Purchased $linkPurchased) {
        return $this->getUrl('*/download/link', array('id' => $linkPurchased->getSerialz(), '_secure' => true));
    }

    /**
     * Return true if target of link new window
     *
     * @return bool
     */
    public function getIsOpenInNewWindow()
    {
        return Mage::getStoreConfigFlag(Faett_Package_Model_Link::XML_PATH_TARGET_NEW_WINDOW);
    }

}
