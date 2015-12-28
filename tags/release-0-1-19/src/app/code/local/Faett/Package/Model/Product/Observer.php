<?php

/**
 * Faett_Package_Model_Product_Observer
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
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Product_Observer
{
    /**
     * This method is invoked after a product was saved in the admin
     * backend and handles the functionality to delete, save or update
     * the product's maintainers.
     *
     * @param $observer The observer instance
     * @return Faett_Package_Model_Product_Observer The instance itself
     */
    public function saveAfter($observer)
    {
        // load the product and the maintainers
        $product = $observer->getEvent()->getProduct();
        $maintainers = $product->getMaintainerIds();
        // load the selected maintainers from the grid
        if (is_array($maintainers)) {
            // initialize the collection with the product's maintainer
            $collection = Mage::getModel('package/package_maintainer')->getCollection();
            $collection->addFieldToFilter('product_id', $product->getId());
            // delete the deselected maintainers
            foreach ($collection as $key => $maintainer) {
                if (!array_key_exists($maintainer->getUserId(), $maintainers)) {
                    $maintainer->delete();
                    unset($maintainers[$key]);
                }
            }
            // save the selected maintainers
            foreach ($maintainers as $maintainerId => $values) {
                $rel = Mage::getModel('package/package_maintainer');
                $rel->setProductId($product->getId());
                $rel->setUserId($maintainerId);
                $rel->setActive($values['active']);
                $rel->save();
            }
        }
        // return the instance
        return $this;
    }
}
