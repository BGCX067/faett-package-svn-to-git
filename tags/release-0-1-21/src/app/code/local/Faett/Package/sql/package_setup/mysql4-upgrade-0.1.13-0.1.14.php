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

$installer->startSetup();

$installer->getConnection()->addColumn($installer->getTable('package/link_purchased'), 'serialz', "varchar(71) NOT NULL default ''");
$installer->getConnection()->addColumn($installer->getTable('package/link_purchased'), 'product_id', "int(10) UNSIGNED NOT NULL default 0");
$installer->getConnection()->addColumn($installer->getTable('package/link_purchased'), 'state', "varchar(50) NOT NULL default ''");

$installer->run(
	"UPDATE faett_package_link_purchased t1, sales_flat_order_item t2
        SET t1.product_id = t2.product_id, t1.state = 'available'
	  WHERE t2.item_id = t1.order_item_id"
);

$conn->addConstraint(
    'FK_PACKAGE_LINK_PURCHASED_PRODUCT', $installer->getTable('package/link_purchased'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$installer->getConnection()->dropColumn($installer->getTable('package/link_purchased'), 'product_sku');

$installer->endSetup();