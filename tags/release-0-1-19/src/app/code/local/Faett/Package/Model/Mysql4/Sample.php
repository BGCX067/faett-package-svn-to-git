<?php

/**
 * Faett_Package_Model_Mysql4_Sample
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
class Faett_Package_Model_Mysql4_Sample extends Mage_Core_Model_Mysql4_Abstract
{

    /**
     * Initialize connection
     *
     */
    protected function  _construct()
    {
        $this->_init('package/sample', 'sample_id');
    }

    /**
     * Save title of sample item in store scope
     *
     * @param Faett_Package_Model_Sample $sampleObject
     * @return Faett_Package_Model_Mysql4_Sample
     */
    public function saveItemTitle($sampleObject)
    {
        $stmt = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/sample_title'))
            ->where('sample_id = ?', $sampleObject->getId())
            ->where('store_id = ?', $sampleObject->getStoreId());
        if ($this->_getReadAdapter()->fetchOne($stmt)) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $sampleObject->getId()) .
                ' AND ' . $this->_getReadAdapter()->quoteInto('store_id = ?', $sampleObject->getStoreId());
            if ($sampleObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->delete(
                    $this->getTable('package/sample_title'), $where);
            } else {
                $this->_getWriteAdapter()->update(
                    $this->getTable('package/sample_title'),
                    array('title' => $sampleObject->getTitle()), $where);
            }
        } else {
            if (!$sampleObject->getUseDefaultTitle()) {
                $this->_getWriteAdapter()->insert(
                    $this->getTable('package/sample_title'),
                    array(
                        'sample_id' => $sampleObject->getId(),
                        'store_id' => $sampleObject->getStoreId(),
                        'title' => $sampleObject->getTitle(),
                    ));
            }
        }
        return $this;
    }

    /**
     * Delete data by item(s)
     *
     * @param Faett_Package_Model_Sample|array|int $items
     * @return Faett_Package_Model_Mysql4_Sample
     */
    public function deleteItems($items)
    {
        $where = '';
        if ($items instanceof Faett_Package_Model_Sample) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $items->getId());
        }
        elseif (is_array($items)) {
            $where = $this->_getReadAdapter()->quoteInto('sample_id in (?)', $items);
        }
        else {
            $where = $this->_getReadAdapter()->quoteInto('sample_id = ?', $items);
        }
        if ($where) {
            $this->_getReadAdapter()->delete(
                $this->getTable('package/sample'),$where);
            $this->_getReadAdapter()->delete(
                $this->getTable('package/sample_title'), $where);
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
            ->from(array('sample' => $this->getMainTable()), null)
            ->join(
                array('sample_title_default' => $this->getTable('package/sample_title')),
                'sample_title_default.sample_id=sample.sample_id AND sample_title_default.store_id=0',
                array())
            ->joinLeft(
                array('sample_title_store' => $this->getTable('package/sample_title')),
                'sample_title_store.sample_id=sample.sample_id AND sample_title_store.store_id=' . intval($storeId),
                array('title' => 'IFNULL(sample_title_store.title, sample_title_default.title)'))
            ->where('sample.product_id=?', $productId);
        if (!$searchData = $this->_getReadAdapter()->fetchCol($select)) {
            $searchData = array();
        }
        return $searchData;
    }
}
