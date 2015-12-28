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

$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'package_file');
$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'dependencies');
$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'stability');
$installer->getConnection()->dropColumn($installer->getTable('package/link_title'), 'version');

$installer->getConnection()->addColumn($installer->getTable('package/link_title'), 'summary', "text NOT NULL default '' AFTER `notes`");
$installer->getConnection()->addColumn($installer->getTable('package/link_title'), 'description', "text NOT NULL default '' AFTER `summary`");

$installer->getConnection()->addColumn($installer->getTable('package/link'), 'package_file', "text NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'dependencies', "text NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'package_size', "int(10) NOT NULL default '0'");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'version', "varchar(25) NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'state', "varchar(25) NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'licence', "varchar(50) NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link'), 'release_date', "datetime NOT NULL default '0000-00-00'");

$installer->endSetup();