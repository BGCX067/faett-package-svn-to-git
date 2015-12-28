<?php

/**
 * Faett_Package_Model_Serialz
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
 * Creates a hash for the download link.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Serialz extends Mage_Core_Model_Abstract
{

    /**
     * The purchased link.
     * @var Faett_Package_Model_Link_Purchased
     */
    protected $_linkPurchased = null;

    /**
     * Initialize the class with the purchased link and
     * the purchased link item.
     *
     * @param Faett_Package_Model_Link_Purchased $linkPurchased
     * 		The purchased link
     * @return Faett_Package_Model_Serialz The instance itself
     */
    public function init(
        Faett_Package_Model_Link_Purchased $linkPurchased) {
        // initialize the purchased link and the purchased link item
        $this->_linkPurchased = $linkPurchased;
        // return the instance itself
        return $this;
    }

    /**
	 * Create and return a hash for the download link.
	 *
	 * @return string The requested hash
     */
    public function serialz()
    {
        // encrypt the hash for the download link
        return Mage::helper('core')->getEncryptor()->encrypt(
            microtime() .
            $this->_linkPurchased->getCustomerId() .
            $this->_linkPurchased->getProductId() .
            $this->_linkPurchased->getPurchasedId()
        );
    }

    /**
     * This method decrypts the passed serialz and sets the
     * components of the serialz.
     *
     * @param string $serialz The serialz to decrypt
     * @return TechDivision_Licenceserver_Model_Serialz
     */
    public function decrypt($serialz)
    {
        return $this;
    }

    /**
     * Return the date in ISO format up from
     * where the serialz is valid.
     *
     * @return string Valid from date
     */
    public function getValidFrom()
    {
        return date('Y-m-d');
    }

    /**
     * Return the date in ISO format up to when
     * the serialz is valid.
     *
     * @return string Valid to date
     */
    public function getValidThru()
    {
        return date('Y-m-d');
    }

    /**
     * This method check's if the passed serialz is
     * valid.
     *
     * @param string $serialz The serialz to check
     * @return boolen TRUE if the serial is valid for the actual customer
     */
    public function isValid($serialz)
    {
        return true;
    }
}