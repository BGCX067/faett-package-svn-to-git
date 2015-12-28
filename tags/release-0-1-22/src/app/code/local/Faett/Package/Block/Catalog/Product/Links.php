<?php

/**
 * Faett_Package_Block_Catalog_Product_Links
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
 * Package Product Links part block.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Block_Catalog_Product_Links
    extends Mage_Catalog_Block_Product_Abstract {

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinksPurchasedSeparately()
    {
        return $this->getProduct()->getLinksPurchasedSeparately();
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function getLinkSelectionRequired()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getLinkSelectionRequired($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @return boolean
     */
    public function hasLinks()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->hasLinks($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @return array
     */
    public function getLinks()
    {
        return $this->getProduct()->getTypeInstance(true)
            ->getLinks($this->getProduct());
    }

    /**
     * Enter description here...
     *
     * @param Faett_Package_Model_Link $link
     * @return string
     */
    public function getFormattedLinkPrice($link)
    {
        $price = $link->getPrice();

        if (0 == $price) {
            return '';
        }

        $_priceInclTax = Mage::helper('tax')->getPrice($link->getProduct(), $price, true);
        $_priceExclTax = Mage::helper('tax')->getPrice($link->getProduct(), $price);

        $priceStr = '<span class="price-notice">+';
        if (Mage::helper('tax')->displayPriceIncludingTax()) {
            $priceStr .= $this->helper('core')->currency($_priceInclTax, true, true);
        } elseif (Mage::helper('tax')->displayPriceExcludingTax()) {
            $priceStr .= $this->helper('core')->currency($_priceExclTax, true, true);
        } elseif (Mage::helper('tax')->displayBothPrices()) {
            $priceStr .= $this->helper('core')->currency($_priceExclTax, true, true);
            if ($_priceInclTax != $_priceExclTax) {
                $priceStr .= ' (+'.$this->helper('core')
                    ->currency($_priceInclTax, true, true).' '.$this->__('Incl. Tax').')';
            }
        }
        $priceStr .= '</span>';

        return $priceStr;
    }

    /**
     * Enter description here...
     *
     * @return string
     */
    public function getJsonConfig()
    {
        $config = array();

        foreach ($this->getLinks() as $link) {
            $config[$link->getId()] = Mage::helper('core')->currency($link->getPrice(), false, false);
        }

        return Zend_Json::encode($config);
    }

    public function getLinkSamlpeUrl($link)
    {
        return $this->getUrl('package/download/linkSample', array('link_id' => $link->getId()));
    }

    /**
     * Return title of links section
     *
     * @return string
     */
    public function getLinksTitle()
    {
        if ($this->getProduct()->getLinksTitle()) {
            return $this->getProduct()->getLinksTitle();
        }
        return Mage::getStoreConfig(Faett_Package_Model_Link::XML_PATH_LINKS_TITLE);
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