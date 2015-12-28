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

// load the product model
$model = Mage::getModel('catalog/product');

// load the entity type ID and the ID of the default attribute set
$entityTypeId = $model->getEntityTypeId();
$attributeSetId = $model->getDefaultAttributeSetId();

// define the name of the new group to add
$groupName = 'Maintainer Information';

// insert a new attribute group
$attributeGroupId = $installer->addAttributeGroup(
    $entityTypeId,
    $attributeSetId,
    $groupName
);

$installer->setConfigData(
	Faett_Package_Helper_Data::FAETT_PACKAGE_MAINTAINER_GROUP_ID,
    $attributeGroupId
);

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/package_maintainer')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/package_maintainer')}` (
  `package_maintainer_id` int(11) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `active` tinyint(1) unsigned NOT NULL default '0',
  PRIMARY KEY  (`package_maintainer_id`),
  KEY `PACKAGE_MAINTAINER_PRODUCT` (`product_id`),
  KEY `PACKAGE_MAINTAINER_CUSTOMER` (`customer_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='Related package maintainers' AUTO_INCREMENT=1 ;
");

$conn->addConstraint(
    'FK_PACKAGE_MAINTAINER_PRODUCT', $installer->getTable('package/package_maintainer'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$conn->addConstraint(
    'FK_PACKAGE_MAINTAINER_CUSTOMER', $installer->getTable('package/package_maintainer'), 'customer_id', $installer->getTable('customer/entity'), 'entity_id'
);

$installer->endSetup();