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

$conn = $installer->getConnection();
/* @var $conn Varien_Db_Adapter_Pdo_Mysql */

$installer->startSetup();

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/link')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/link')}` (
  `link_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `sort_order` int(10) unsigned NOT NULL default '0',
  `number_of_downloads` int(10) unsigned default NULL,
  `is_shareable` smallint(1) unsigned NOT NULL default '0',
  `link_url` varchar(255) NOT NULL default '',
  `link_file` varchar(255) NOT NULL default '',
  `link_type` varchar(20) NOT NULL default '',
  `sample_url` varchar(255) NOT NULL default '',
  `sample_file` varchar(255) NOT NULL default '',
  `sample_type` varchar(20) NOT NULL default '',
  PRIMARY KEY  (`link_id`),
  KEY `PACKAGE_LINK_PRODUCT` (`product_id`),
  KEY `PACKAGE_LINK_PRODUCT_SORT_ORDER` (`product_id`,`sort_order`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
");

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/link_price')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/link_price')}` (
  `price_id` int(10) unsigned NOT NULL auto_increment,
  `link_id` int(10) unsigned NOT NULL default '0',
  `website_id` smallint(5) unsigned NOT NULL default '0',
  `price` decimal(12,4) NOT NULL default '0.0000',
  PRIMARY KEY  (`price_id`),
  KEY `PACKAGE_LINK_PRICE_LINK` (`link_id`),
  KEY `PACKAGE_LINK_PRICE_WEBSITE` (`website_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
");

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/link_purchased')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/link_purchased')}` (
  `purchased_id` int(10) unsigned NOT NULL auto_increment,
  `order_id` int(10) unsigned NOT NULL default '0',
  `order_increment_id` varchar(50) NOT NULL default '',
  `order_item_id` int(10) unsigned NOT NULL default '0',
  `created_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `updated_at` datetime NOT NULL default '0000-00-00 00:00:00',
  `customer_id` int(10) unsigned NOT NULL default '0',
  `product_name` varchar(255) NOT NULL default '',
  `product_sku` varchar(255) NOT NULL default '',
  `link_section_title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`purchased_id`),
  KEY `PACKAGE_ORDER_ID` (`order_id`),
  KEY `PACKAGE_CUSTOMER_ID` (`customer_id`),
  KEY `PACKAGE_ORDER_ITEM_ID` (`order_item_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
");

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/link_title')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/link_title')}` (
  `title_id` int(10) unsigned NOT NULL auto_increment,
  `link_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`title_id`),
  KEY `PACKAGE_LINK_TITLE_LINK` (`link_id`),
  KEY `PACKAGE_LINK_TITLE_STORE` (`store_id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=3 ;
");

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/sample')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/sample')}` (
  `sample_id` int(10) unsigned NOT NULL auto_increment,
  `product_id` int(10) unsigned NOT NULL default '0',
  `sample_url` varchar(255) NOT NULL default '',
  `sample_file` varchar(255) NOT NULL default '',
  `sample_type` varchar(20) NOT NULL default '',
  `sort_order` int(10) unsigned NOT NULL default '0',
  PRIMARY KEY  (`sample_id`),
  KEY `PACKAGE_SAMPLE_PRODUCT` (`product_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$installer->run("
DROP TABLE IF EXISTS `{$installer->getTable('package/sample_title')}`;
CREATE TABLE IF NOT EXISTS `{$installer->getTable('package/sample_title')}` (
  `title_id` int(10) unsigned NOT NULL auto_increment,
  `sample_id` int(10) unsigned NOT NULL default '0',
  `store_id` smallint(5) unsigned NOT NULL default '0',
  `title` varchar(255) NOT NULL default '',
  PRIMARY KEY  (`title_id`),
  KEY `PACKAGE_SAMPLE_TITLE_SAMPLE` (`sample_id`),
  KEY `PACKAGE_SAMPLE_TITLE_STORE` (`store_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1 ;
");

$conn->addConstraint(
    'FK_PACKAGE_LINK_PRODUCT', $installer->getTable('package/link'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$conn->addConstraint(
    'FK_PACKAGE_LINK_PRICE_LINK', $installer->getTable('package/link_price'), 'link_id', $installer->getTable('package/link'), 'link_id'
);

$conn->addConstraint(
    'FK_PACKAGE_LINK_PRICE_WEBSITE', $installer->getTable('package/link_price'), 'website_id', $installer->getTable('core/website'), 'website_id'
);

$conn->addConstraint(
    'FK_PACKAGE_ORDER_ID', $installer->getTable('package/link_purchased'), 'order_id', $installer->getTable('sales/order'), 'entity_id'
);

$conn->addConstraint(
    'FK_PACKAGE_ORDER_ITEM_ID', $installer->getTable('package/link_purchased'), 'order_item_id', $installer->getTable('sales/order_item'), 'item_id'
);

$conn->addConstraint(
    'FK_PACKAGE_LINK_TITLE_LINK', $installer->getTable('package/link_title'), 'link_id', $installer->getTable('package/link'), 'link_id'
);
$conn->addConstraint(
    'FK_PACKAGE_LINK_TITLE_STORE', $installer->getTable('package/link_title'), 'store_id', $installer->getTable('core/store'), 'store_id'
);

$conn->addConstraint(
    'FK_PACKAGE_SAMPLE_PRODUCT', $installer->getTable('package/sample'), 'product_id', $installer->getTable('catalog/product'), 'entity_id'
);

$conn->addConstraint(
    'FK_PACKAGE_SAMPLE_TITLE_SAMPLE', $installer->getTable('package/sample_title'), 'sample_id', $installer->getTable('package/sample'), 'sample_id'
);

$conn->addConstraint(
    'FK_PACKAGE_SAMPLE_TITLE_STORE', $installer->getTable('package/sample_title'), 'store_id', $installer->getTable('core/store'), 'store_id'
);

$fieldList = array(
    'price',
    'special_price',
    'special_from_date',
    'special_to_date',
    'minimal_price',
    'cost',
    'tier_price',
    'tax_class_id'
);

// make these attributes applicable to downloadable products
foreach ($fieldList as $field) {
    $applyTo = explode(',', $installer->getAttribute('catalog_product', $field, 'apply_to'));
    if (!in_array('package', $applyTo)) {
        $applyTo[] = 'package';
        $installer->updateAttribute('catalog_product', $field, 'apply_to', join(',', $applyTo));
    }
}

$installer->endSetup();