<?php

/**
 * Faett_Package_Model_Mysql4_Sample_Collection
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
 * Package samples resource collection.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Mysql4_Sample_Collection
    extends Mage_Core_Model_Mysql4_Collection_Abstract {

    /**
     * Enter description here...
     *
     */
    protected function _construct()
    {
        $this->_init('package/sample');
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product|array|integer|null $product
     * @return Faett_Package_Model_Mysql4_Sample_Collection
     */
    public function addProductToFilter($product)
    {
        if (empty($product)) {
            $this->addFieldToFilter('product_id', '');
        } elseif (is_array($product)) {
            $this->addFieldToFilter('product_id', array('in' => $product));
        } elseif ($product instanceof Mage_Catalog_Model_Product) {
            $this->addFieldToFilter('product_id', $product->getId());
        } else {
            $this->addFieldToFilter('product_id', $product);
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param integer $storeId
     * @return Faett_Package_Model_Mysql4_Sample_Collection
     */
    public function addTitleToResult($storeId=0)
    {
        $this->getSelect()
            ->joinLeft(array('default_title_table' => $this->getTable('package/sample_title')),
                '`default_title_table`.sample_id=`main_table`.sample_id AND `default_title_table`.store_id = 0',
                array('default_title' => 'title'))
            ->joinLeft(array('store_title_table' => $this->getTable('package/sample_title')),
                '`store_title_table`.sample_id=`main_table`.sample_id AND `store_title_table`.store_id = ' . intval($storeId),
                array('store_title' => 'title','title' => new Zend_Db_Expr('IFNULL(`store_title_table`.title, `default_title_table`.title)')))
            ->order('main_table.sort_order ASC')
            ->order('title ASC');

        return $this;
    }

}
