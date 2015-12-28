<?php

/**
 * Faett_Package_Model_Observer
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
 * Package Products Observer.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Observer
{
    const XML_PATH_DISABLE_GUEST_CHECKOUT   = 'catalog/package/disable_guest_checkout';

    /**
     * Prepare product to save
     *
     * @param   Varien_Object $observer
     * @return  Faett_Package_Model_Observer
     */
    public function prepareProductSave($observer)
    {
        $request = $observer->getEvent()->getRequest();
        $product = $observer->getEvent()->getProduct();

        if ($package = $request->getPost('package')) {
            $product->setPackageData($package);
        }

        return $this;
    }

    /**
     * Save data from order to purchased links
     *
     * @param Varien_Object $observer
     * @return Faett_Package_Model_Observer
     */
    public function savePackageOrderItem($observer)
    {
    	// load the order item
        $orderItem = $observer->getEvent()->getItem();
		// check if the product had already been purchased
        if (Mage::getModel('package/link_purchased')->load($orderItem->getId(), 'order_item_id')->getId()) {
            return $this;
        }
		// load the product itself
        $product = Mage::getModel('catalog/product')
            ->setStoreId($orderItem->getOrder()->getStoreId())
            ->load($orderItem->getProductId());
		// check if the product is of type package
        if ($product->getTypeId() == Faett_Package_Model_Product_Type::TYPE_PACKAGE) {
            	// initialize a new purchased link
                $linkPurchased = Mage::getModel('package/link_purchased');
				// copy the data from the order
              	Mage::helper('core')->copyFieldset(
                	'package_sales_copy_order',
                    'to_package',
                    $orderItem->getOrder(),
                    $linkPurchased
                );
				// copy the data from the order item
                Mage::helper('core')->copyFieldset(
                    'package_sales_copy_order_item',
                    'to_package',
                    $orderItem,
                	$linkPurchased
                );
				// initialize the title
                $linkSectionTitle = (
                $product->getLinksTitle() ?
                $product->getLinksTitle() : Mage::getStoreConfig(
                	Faett_Package_Model_Link::XML_PATH_LINKS_TITLE
            	)
            );
    		// create a serial number
            $serialz = Mage::getModel('package/serialz')
            	->init($linkPurchased)
                ->serialz();
			// create the purchased link
            $linkPurchased
            	->setLinkSectionTitle($linkSectionTitle)
            	->setState(Faett_Package_Model_Link_Purchased::LINK_STATE_PENDING)
                ->setSerialz($serialz)
                ->save();
        }
		// return the instance
        return $this;
    }

    /**
     * Set checkout session flag if order has package product(s)
     *
     * @param Varien_Object $observer
     * @return Faett_Package_Model_Observer
     */
    public function setHasPackageProducts($observer)
    {
        $session = Mage::getSingleton('checkout/session');
        if (!$session->getHasPackageProducts()) {
            $order = $observer->getEvent()->getOrder();
            foreach ($order->getAllItems() as $item) {
                /* @var $item Mage_Sales_Model_Order_Item */
                if ($item->getProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE
                || $item->getRealProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE
                || $item->getProductOptionByCode('is_package'))
                {
                    $session->setHasPackageProducts(true);
                    break;
                }
            }
        }
        return $this;
    }

    /**
     * Set status of link
     *
     * @param Varien_Object $observer
     * @return Faett_Package_Model_Observer
     */
    public function setLinkStatus($observer)
    {
    	// load the order
        $order = $observer->getEvent()->getOrder();
		// initialize the state
        $state = '';
		// initialize an array for the order items
        $orderItemsIds = array();
		// initialize the order state to enable the download
        $orderItemStateToEnable = Mage::getStoreConfig(Faett_Package_Model_Link_Purchased::XML_PATH_ORDER_STATE, $order->getStoreId());
        // check the order state
        if ($order->getState() == Mage_Sales_Model_Order::STATE_HOLDED) {
            $state = Faett_Package_Model_Link_Purchased::LINK_STATE_PENDING;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_CANCELED ||
            $order->getState() == Mage_Sales_Model_Order::STATE_CLOSED) {
            $state = Faett_Package_Model_Link_Purchased::LINK_STATE_EXPIRED;
        } elseif ($order->getState() == Mage_Sales_Model_Order::STATE_PENDING_PAYMENT) {
            $state = Faett_Package_Model_Link_Purchased::LINK_STATE_PENDING_PAYMENT;
        } else {
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE
                    || $item->getRealProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE)
                {
                    if ($item->getStatusId() == $orderItemStateToEnable) {
                        $orderItemsIds[] = $item->getId();
                    }
                }
            }
            if ($orderItemsIds) {
                $state = Faett_Package_Model_Link_Purchased::LINK_STATE_AVAILABLE;
            }
        }
        // check the product type -> has to be a package product
        if (!$orderItemsIds && $state) {
            foreach ($order->getAllItems() as $item) {
                if ($item->getProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE
                    || $item->getRealProductType() == Faett_Package_Model_Product_Type::TYPE_PACKAGE)
                {
                    $orderItemsIds[] = $item->getId();
                }
            }
        }
		// enable the downloads
        if ($orderItemsIds) {
            $linkPurchased = Mage::getResourceModel('package/link_purchased_collection')
                ->addFieldToFilter('order_item_id', array('in' => $orderItemsIds));
            foreach ($linkPurchased as $link) {
                if ($link->getState() != Faett_Package_Model_Link_Purchased::LINK_STATE_EXPIRED) {
                    $link->setState($state)
                    ->save();
                }
            }
        }
		// return the instance
        return $this;
    }

    /**
     * Check is allowed guest checkuot if quote contain package product(s)
     *
     * @param Varien_Event_Observer $observer
     * @return Faett_Package_Model_Observer
     */
    public function isAllowedGuestCheckout(Varien_Event_Observer $observer)
    {
        $quote  = $observer->getEvent()->getQuote();
        /* @var $quote Mage_Sales_Model_Quote */
        $store  = $observer->getEvent()->getStore();
        $result = $observer->getEvent()->getResult();

        $isContain = false;

        foreach ($quote->getAllItems() as $item) {
            if (($product = $item->getProduct()) &&
            $product->getTypeId() == Faett_Package_Model_Product_Type::TYPE_PACKAGE) {
                $isContain = true;
            }
        }

        if ($isContain && Mage::getStoreConfigFlag(self::XML_PATH_DISABLE_GUEST_CHECKOUT, $store)) {
            $result->setIsAllowed(false);
        }

        return $this;
    }
}
