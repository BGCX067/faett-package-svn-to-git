<?php

/**
 * Faett_Package_Adminhtml_ProductController
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

require_once "Mage/Adminhtml/controllers/Catalog/ProductController.php";

/**
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Adminhtml_ProductController
    extends Mage_Adminhtml_Catalog_ProductController {

    /**
     * Get maintainers grid and serializer block
     */
    public function maintainerAction()
    {

        $this->_initProduct();

        $gridBlock = $this->getLayout()->createBlock('package/adminhtml_catalog_product_edit_tab_maintainer')
            ->setGridUrl($this->getUrl('*/*/gridOnly', array('_current' => true, 'gridOnlyBlock' => 'maintainer')));

        $collection = Mage::getModel('package/package_maintainer')->getCollection();
        $collection->addFieldToFilter('product_id', Mage::registry('product')->getId());
        $collection->load();

        $serializerBlock = $this->getLayout()->createBlock('package/adminhtml_catalog_product_edit_tab_ajax_serializer')
            ->setGridBlock($gridBlock)
            ->setMaintainers($collection)
            ->setInputElementName('links[maintainer]');

        $this->_outputBlocks($gridBlock, $serializerBlock);
    }

    /**
     * Get specified tab grid
     */
    public function gridOnlyAction()
    {
        if ($this->getRequest()->getParam('gridOnlyBlock') == 'maintainer') {
            $this->_initProduct();
            $this->loadLayout();
            $this->getResponse()->setBody(
                $this->getLayout()->createBlock('package/adminhtml_catalog_product_edit_tab_' . $this->getRequest()->getParam('gridOnlyBlock'))
                    ->toHtml()
            );
        } else {
            parent::gridOnlyAction();
        }
    }

    /**
     * Initialize product before saving
     */
    protected function _initProductSave()
    {

        /**
         * Init maintainer links
         */
        $links = $this->getRequest()->getPost('links');

        $product = parent::_initProductSave();

        if (isset($links['maintainer']) /* && !$product->getMaintainerReadonly() */ ) {
            $maintainerIds = $this->_decodeInput($links['maintainer']);
            $product->setMaintainerIds($maintainerIds);
        }

        return $product;
    }
}