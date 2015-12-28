<?php

/**
 * Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Maintainer
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
 * Crossell products admin grid.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Maintainer
    extends Mage_Adminhtml_Block_Widget_Grid
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Initializes the Block.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
        $this->setId('package_maintainer_grid');
        $this->setDefaultSort('user_id');
        $this->setUseAjax(true);
        if ($this->_getProduct()->getId()) {
            $this->setDefaultFilter(
                array('in_maintainers' => 1)
            );
        }
    }

    /**
     * Retirve currently edited product model
     *
     * @return Mage_Catalog_Model_Product
     */
    protected function _getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Grid#_addColumnFilterToCollection($column)
     */
    protected function _addColumnFilterToCollection($column)
    {
        // Set custom filter for in maintainer flag
        if ($column->getId() == 'in_maintainers') {
            // load the id's of the product's maintainers
            $maintainerIds = $this->_getSelectedMaintainers();
            if (empty($maintainerIds)) {
                $maintainerIds = 0;
            }
            // add the filter
            if ($column->getFilter()->getValue()) {
                $this->getCollection()->addFieldToFilter(
                	'user_id', array('in' => $maintainerIds)
                );
            } else {
                if($maintainerIds) {
                    $this->getCollection()->addFieldToFilter(
                    	'user_id', array('nin' => $maintainerIds)
                    );
                }
            }
        } else {
            parent::_addColumnFilterToCollection($column);
        }
        return $this;
    }

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Grid#_prepareCollection()
     */
    protected function _prepareCollection()
    {
        // load the customer collection
        $collection = Mage::getResourceModel('api/user_collection');
        // check if the product is in readonly mode
        if ($this->isReadonly()) {
            $maintainerIds = $this->_getSelectedMaintainers();
            if (empty($maintainerIds)) {
                $maintainerIds = array(0);
            }
            $collection->addFieldToFilter(
            	'user_id', array('in' => $maintainerIds)
            );
        }
        // set the collection
        $this->setCollection($collection);
        // call the super class
        return parent::_prepareCollection();
    }

    /**
     * Checks when this block is readonly
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return false;
    }


    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Grid#_prepareColuns()
     */
    protected function _prepareColumns()
    {
        if (!$this->isReadonly()) {
            $this->addColumn(
            	'in_maintainers',
                array(
                    'header_css_class' => 'a-center',
                    'type'      => 'checkbox',
                    'name'      => 'in_maintainers',
                    'values'    => $this->_getSelectedMaintainers(),
                    'align'     => 'center',
                    'index'     => 'user_id'
                )
            );
        }

        $this->addColumn(
        	'entity_id',
            array(
                'header'    => Mage::helper('package')->__('ID'),
                'width'     => '50px',
                'index'     => 'user_id',
                'type'      => 'number',
            )
        );

        $this->addColumn(
        	'firstname',
            array(
                'header'    => Mage::helper('package')->__('Firstname'),
                'index'     => 'firstname'
            )
        );

        $this->addColumn(
        	'lastname',
            array(
                'header'    => Mage::helper('package')->__('Lastname'),
                'index'     => 'lastname'
            )
        );

        $this->addColumn(
        	'username',
            array(
                'header'    => Mage::helper('package')->__('Username'),
                'index'     => 'username'
            )
        );

        $this->addColumn(
        	'email',
            array(
                'header'    => Mage::helper('package')->__('Email'),
                'index'     => 'email'
            )
        );

       $this->addColumn(
           'active',
           array(
               'header'         => Mage::helper('package')->__('Active'),
               'width'          => '100',
               'index'          => 'active_maintainer',
               'type'		    => 'select',
               'validate_class' => 'validate-number',
               'options'	    => array(0 => 'No', 1 => 'Yes'),
               'sortable'	    => false,
               'filter'         => false,
            )
        );

        $this->addColumn('action',
            array(
                'header'    =>  Mage::helper('package')->__('Action'),
                'width'     => '100',
                'type'      => 'action',
                'getter'    => 'getId',
                'actions'   => array(
                    array(
                        'caption'   => Mage::helper('package')->__('Edit'),
                        'url'       => array('base'=> '*/api_user/edit'),
                        'field'     => 'user_id'
                    )
                ),
                'filter'    => false,
                'sortable'  => false,
                'index'     => 'stores',
                'is_system' => true,
        ));

        $this->addExportType('*/*/exportCsv', Mage::helper('package')->__('CSV'));
        $this->addExportType('*/*/exportXml', Mage::helper('package')->__('XML'));
        return parent::_prepareColumns();
    }

    /**
     * (non-PHPdoc)
     * @see app/code/core/Mage/Adminhtml/Block/Widget/Mage_Adminhtml_Block_Widget_Grid#getGridUrl()
     */
    public function getGridUrl()
    {
        return $this->_getData('grid_url') ? $this->_getData('grid_url') : $this->getUrl('package/adminhtml_product/maintainer', array('_current'=>true));
    }

    /**
     * Returns an array with the id's of the maintainers
     * selected for the actual package.
     *
     * @return array The id's of the products maintainers
     */
    protected function _getSelectedMaintainers()
    {
        // check if maintainers should be loaded
        $maintainers = $this->getRequest()->getPost('maintainers', null);
        // if the maintainers are requested
        if (!is_array($maintainers)) {
            // load the collection and set the filter to use
            $collection = Mage::getModel('package/package_maintainer')->getCollection();
            $collection->addFieldToFilter('product_id', $this->_getProduct()->getId());
            // assemble an array with the id's of the selected maintainers
            foreach ($collection as $maintainer) {
                $maintainers[] = $maintainer->getUserId();
            }
        }
        // return the id's
        return $maintainers;
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('package')->__('Maintainers');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('package')->__('Maintainers');
    }

    /**
     * Check if tab can be displayed
     *
     * @return boolean
     */
    public function canShowTab()
    {
        return true;
    }

    /**
     * Check if tab is hidden
     *
     * @return boolean
     */
    public function isHidden()
    {
        return false;
    }
}