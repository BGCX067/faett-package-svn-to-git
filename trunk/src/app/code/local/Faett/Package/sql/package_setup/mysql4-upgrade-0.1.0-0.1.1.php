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
$groupName = 'Package Information';

// insert a new attribute group
$attributeGroupId = $installer->addAttributeGroup(
    $entityTypeId,
    $attributeSetId,
    $groupName
);

$installer->setConfigData(
	Faett_Package_Helper_Data::FAETT_PACKAGE_LINK_GROUP_ID,
    $attributeGroupId
);

$option = array(
	'value' => array(
        array(0 => 'Package', 0 => 'Extension'),
    )
);

$installer->addAttribute('catalog_product', 'package_category', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Package Category',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'eav/entity_attribute_source_table',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'option'			=> $option,
        'position'			=> 1
    ));

$installer->addAttribute('catalog_product', 'licence', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Licence',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'position'			=> 2
    ));

$installer->addAttribute('catalog_product', 'licence_uri', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Licence URI',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => true,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'position'			=> 3
    ));

$installer->addAttribute('catalog_product', 'deprecated', array(
        'type'              => 'int',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Deprecated',
        'input'             => 'select',
        'class'             => '',
        'source'            => 'eav/entity_attribute_source_boolean',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => true,
        'user_defined'      => false,
        'default'           => 0,
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'position'			=> 4
    ));

$installer->addAttribute('catalog_product', 'deprecated_channel', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Channel (if deprecated)',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'position'			=> 5
    ));

$installer->addAttribute('catalog_product', 'deprecated_package', array(
        'type'              => 'varchar',
        'backend'           => '',
        'frontend'          => '',
        'label'             => 'Package (if deprecated)',
        'input'             => 'text',
        'class'             => '',
        'source'            => '',
        'global'            => Mage_Catalog_Model_Resource_Eav_Attribute::SCOPE_STORE,
        'visible'           => true,
        'required'          => false,
        'user_defined'      => false,
        'default'           => '',
        'searchable'        => true,
        'filterable'        => true,
        'comparable'        => true,
        'visible_on_front'  => true,
        'unique'            => false,
        'apply_to'          => 'package',
        'is_configurable'   => false,
        'group'			 	=> $groupName,
        'position'			=> 6
    ));

$installer->endSetup();