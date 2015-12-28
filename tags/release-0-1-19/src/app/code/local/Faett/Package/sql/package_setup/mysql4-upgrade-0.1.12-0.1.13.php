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

$installer->getConnection()->dropColumn($installer->getTable('package/package_maintainer'), 'customer_id');
$installer->getConnection()->addColumn($installer->getTable('package/package_maintainer'), 'user_id', "mediumint(9) unsigned NOT NULL default '0'");

$conn->raw_query('TRUNCATE TABLE ' . $installer->getTable('package/package_maintainer'));

$conn->addConstraint(
    'FK_PACKAGE_MAINTAINER_API_USER', $installer->getTable('package/package_maintainer'), 'user_id', $installer->getTable('api/user'), 'user_id'
);

$installer->endSetup();