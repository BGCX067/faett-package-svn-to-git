<?php

/**
 * Faett_Package
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

$installer = $this;
/* @var $installer Faett_Package_Model_Resource_Eav_Mysql4_Setup */

$installer->startSetup();

$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'notes');
$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'summary');
$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'description');

$installer->getConnection()->addColumn($installer->getTable('package/link'), 'notes', "text NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'summary', "text NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'description', "text NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'package_name', "varchar(100) NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'licence_uri', "varchar(255) NOT NULL default ''");

$installer->endSetup();