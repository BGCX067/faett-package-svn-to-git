<?php

/**
 * Faett_Package_Model_Mysql4_Link_Title
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
 * Package Product link purchased resource model.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Mysql4_Link_Title
    extends Mage_Core_Model_Mysql4_Abstract {

    /**
     * Varien class constructor.
     *
     * @return void
     */
    protected function  _construct()
    {
        $this->_init('package/link_title', 'title_id');
    }

    /**
	 * Loads the link title by the passed
	 * link and store ID.
	 *
	 * @param Faett_Package_Model_Link_Title $linkTitle The link title to load
	 * @param int $linkId The link ID to load the title for
	 * @param int $storeId The store ID to load the title for
	 * @param void
     */
    public function loadByLinkIdAndStoreId(
        Faett_Package_Model_Link_Title $linkTitle,
        $linkId,
        $storeId) {
        // initialize the SQL for loading the link title by its name
        $select = $this->_getReadAdapter()->select()
            ->from($this->getTable('package/link_title'), array($this->getIdFieldName()))
            ->where('link_id=:link_id')
            ->where('store_id=:store_id');
		// try to load the link title by its link ID
        if ($id = $this->_getReadAdapter()->fetchOne($select, array('link_id' => $linkId, 'store_id' => $storeId))) {
            // use the found data to initialize the instance
            $this->load($linkTitle, $id);
        } else {
            if ($id = $this->_getReadAdapter()->fetchOne($select, array('link_id' => $linkId, 'store_id' => 0))) {
                // use the found data to initialize the instance
                $this->load($linkTitle, $id);
            }
        }
    }
}