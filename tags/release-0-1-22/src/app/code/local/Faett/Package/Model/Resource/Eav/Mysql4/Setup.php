<?php

/**
 * Faett_Package_Model_Resource_Eav_Mysql4_Setup
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
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Resource_Eav_Mysql4_Setup
    extends Mage_Eav_Model_Entity_Setup {

    /**
     * Add Attribute Group
     *
     * @param mixed $entityTypeId
     * @param mixed $setId
     * @param string $name
     * @param int $sortOrder
     * @return integer The ID of the last created attribute group
     * @see Mage_Eav_Model_Entity_Setup::addAttributeGroup($entityTypeId, $setId, $name, $sortOrder=null)
     */
    public function addAttributeGroup(
        $entityTypeId,
        $setId,
        $name,
        $sortOrder = null) {
        // add the attribute group
        Mage_Eav_Model_Entity_Setup::addAttributeGroup($entityTypeId, $setId, $name, $sortOrder);
        // load the ID of the last created attribute group
        return $this->_conn->lastInsertId();
    }
}