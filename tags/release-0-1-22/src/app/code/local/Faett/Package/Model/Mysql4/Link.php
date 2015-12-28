<?php

/**
 * Faett_Package_Model_Mysql4_Link
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
 * Package Product  Samples resource model.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Mysql4_Link extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Initialize connection and define resource
     *
     */
    protected function  _construct()
    {
        $this->_init('package/link', 'link_id');
    }

    /**
     * Save title and price of link item
     *
     * @param Faett_Package_Model_Link $linkObject
     * @return Faett_Package_Model_Mysql4_link
     */
    public function saveItemTitleAndPrice($linkObject)
    {
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/link_title'))
            ->where('link_id = ?', $linkObject->getId())
            ->where('store_id = ?', $linkObject->getStoreId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $where = $this->_getReadAdapter()->quoteInto('link_id = ?', $linkObject->getId()) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $linkObject->getStoreId());
            if ($linkObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->delete(
                    $this->getTable('package/link_title'), $where);
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getTable('package/link_title'),
                    array(
                    	'title' => $linkObject->getTitle()
                    ),
                    $where
                );
            }
        } else {
            if (!$linkObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('package/link_title'),
                    array(
                        'link_id' => $linkObject->getId(),
                        'store_id' => $linkObject->getStoreId(),
                        'title' => $linkObject->getTitle()
                    ));
            }
        }
        $stmt = null;
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/link_price'))
            ->where('link_id = ?', $linkObject->getId())
            ->where('website_id = ?', $linkObject->getWebsiteId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $where = $this->_getReadAdapter()->quoteInto('link_id = ?', $linkObject->getId()) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('website_id = ?', $linkObject->getWebsiteId());
            if ($linkObject->getUseDefaultPrice()) {
                $this->_getReadAdapter()->delete(
                    $this->getTable('package/link_price'), $where);
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getTable('package/link_price'),
                    array('price' => $linkObject->getPrice()), $where);
            }
        } else {
            if (!$linkObject->getUseDefaultPrice()) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('package/link_price'),
                    array(
                        'link_id' => $linkObject->getId(),
                        'website_id' => $linkObject->getWebsiteId(),
                        'price' => $linkObject->getPrice()
                    ));
            }
        }
        return $this;
    }

    /**
     * Delete data by item(s)
     *
     * @param Faett_Package_Model_Link|array|int $items
     * @return Faett_Package_Model_Mysql4_Link
     */
    public function deleteItems($items)
    {
        $where = '';
        if ($items instanceof Faett_Package_Model_Link) {
            $where = $this->_getReadAdapter()->quoteInto('link_id = ?', $items->getId());
        }
        elseif (is_array($items)) {
            $where = $this->_getReadAdapter()->quoteInto('link_id in (?)', $items);
        }
        else {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $items);
        }
        if ($where) {
            $this->_getWriteAdapter()->delete(
                $this->getTable('package/link'), $where);
            $this->_getWriteAdapter()->delete(
                $this->getTable('package/link_title'), $where);
            $this->_getWriteAdapter()->delete(
                $this->getTable('package/link_price'), $where);
        }
        return $this;
    }

    /**
     * Retrieve links searchable data
     *
     * @param int $productId
     * @param int $storeId
     * @return array
     */
    public function getSearchableData($productId, $storeId)
    {
        $select = $this->_getReadAdapter()->select()
            ->from(array('link' => $this->getMainTable()), null)
            ->join(
                array('link_title_default' => $this->getTable('package/link_title')),
                'link_title_default.link_id=link.link_id AND link_title_default.store_id=0',
                array())
            ->joinLeft(
                array('link_title_store' => $this->getTable('package/link_title')),
                'link_title_store.link_id=link.link_id AND link_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(link_title_store.title, link_title_default.title)'))
            ->where('link.product_id=?', $productId);
        if (!$searchData = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array();
        }
        return $searchData;
    }

    /**
     * Loads the link for the passed product ID and version.
	 *
	 * @param Faett_Package_Model_Link $link The link to load
	 * @param integer $productId The product ID to load the link for
	 * @param string $version The version to load the link for
	 * @param void
     */
    public function loadByProductIdAndVersion(
        Faett_Package_Model_Link $link,
        $productId,
        $version) {
        // initialize the SQL for loading the link by its version
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/link'), array($this->getIdFieldName()))
            ->where('product_id=:productId')
            ->where('version=:version');
		// try to load the link title by its link ID
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('productId' => $productId, 'version' => $version))) {
            // use the found data to initialize the instance
            $this->load($link, $id);
        }
    }
}