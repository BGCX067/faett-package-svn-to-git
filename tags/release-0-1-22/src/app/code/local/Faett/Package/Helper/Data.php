<?php

/**
 * Faett_Package_Helper_Data
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
 * Package helper.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Helper_Data extends Mage_Core_Helper_Abstract
{

    /**
     * The path with the group ID for replacing the default package link tab.
     * @var string
     */
    const FAETT_PACKAGE_LINK_GROUP_ID = 'faett/package/link_group_id';

    /**
     * The path with the group ID for replacing the default package maintainer tab.
     * @var string
     */
    const FAETT_PACKAGE_MAINTAINER_GROUP_ID = 'faett/package/maintainer_group_id';

    /**
     * Check is link shareable or not
     *
     * @param Faett_Package_Model_Link $link
     * @return bool
     */
    public function getIsShareable($link)
    {
        $shareable = false;
        switch ($link->getIsShareable()) {
            case Faett_Package_Model_Link::LINK_SHAREABLE_YES:
            case Faett_Package_Model_Link::LINK_SHAREABLE_NO:
                $shareable = (bool) $link->getIsShareable();
                break;
            case Faett_Package_Model_Link::LINK_SHAREABLE_CONFIG:
                $shareable = (bool) Mage::getStoreConfigFlag(Faett_Package_Model_Link::XML_PATH_CONFIG_IS_SHAREABLE);
        }
        return $shareable;
    }
}
