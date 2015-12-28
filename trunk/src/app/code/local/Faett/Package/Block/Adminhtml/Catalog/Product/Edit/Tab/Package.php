<?php

/**
 * Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Package
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
 * Adminhtml catalog product package items tab and form.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Block_Adminhtml_Catalog_Product_Edit_Tab_Package
    extends Mage_Adminhtml_Block_Catalog_Form
    implements Mage_Adminhtml_Block_Widget_Tab_Interface {

    /**
     * Reference to product objects that is being edited
     *
     * @var Mage_Catalog_Model_Product
     */
    protected $_product = null;

    protected $_config = null;


    /**
     * Class constructor
     *
     */
    public function __construct()
    {
        parent::__construct();
        $this->setTemplate('package/product/edit/package.phtml');
    }

    /**
     * Check is readonly block
     *
     * @return boolean
     */
    public function isReadonly()
    {
        return $this->getProduct()->getPackageReadonly();
    }

    /**
     * Retrieve product
     *
     * @return Mage_Catalog_Model_Product
     */
    public function getProduct()
    {
        return Mage::registry('current_product');
    }

    /**
     * Get tab label
     *
     * @return string
     */
    public function getTabLabel()
    {
        return Mage::helper('package')->__('Releases');
    }

    /**
     * Get tab title
     *
     * @return string
     */
    public function getTabTitle()
    {
        return Mage::helper('package')->__('Releases');
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

    /**
     * Comparison method to sort the product attributes
     * by their position.
     *
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $o1
     * @param Mage_Catalog_Model_Resource_Eav_Attribute $o2
     * @return int 0 if the position is equal, +1 if position of $o1 is greater, else -1
     */
    protected function _cmp($o1, $o2)
    {
        // load an compare the positions
        $a = $o1->getPosition();
        $b = $o2->getPosition();
        if ($a == $b) {
            return 0;
        }
        return ($a > $b) ? +1 : -1;
    }

    protected function _prepareForm()
    {

        $product = $this->getProduct();

        if (!($setId = $product->getAttributeSetId())) {
            $setId = $this->getRequest()->getParam('set', null);
        }

        if ($setId) {

            $attributeGroupId = Mage::getConfig()->getNode(
                Faett_Package_Helper_Data::FAETT_PACKAGE_LINK_GROUP_ID,
                'default',
                0
            );

            $group = Mage::getModel(
            	'eav/entity_attribute_group'
            )->load($attributeGroupId);

            $attributes = $product->getAttributes($group->getId(), true);

            // do not add groups without attributes
            foreach ($attributes as $key => $attribute) {
                if( !$attribute->getIsVisible() ) {
                    unset($attributes[$key]);
                }
            }

            // sort the attributes after their position
            usort($attributes, array($this, '_cmp'));


            if (count($attributes) == 0) {
                return;
            }

            $form = new Varien_Data_Form();

            // initialize product object as form property for using it in elements generation
            $form->setDataObject(Mage::registry('product'));

            $fieldset = $form->addFieldset('group_fields'.$group->getId(),
                array('legend'=>Mage::helper('catalog')->__($group->getAttributeGroupName()))
            );

            $this->_setFieldset($attributes, $fieldset, array('gallery'));

            if (!$form->getElement('media_gallery') &&
                Mage::getSingleton('admin/session')->isAllowed('catalog/attributes/attributes')) {
                $headerBar = $this->getLayout()->createBlock(
                    'adminhtml/catalog_product_edit_tab_attributes_create'
                );

                $headerBar->getConfig()
                    ->setTabId('group_' . $group->getId())
                    ->setGroupId($group->getId())
                    ->setStoreId($form->getDataObject()->getStoreId())
                    ->setAttributeSetId($form->getDataObject()->getAttributeSetId())
                    ->setTypeId($form->getDataObject()->getTypeId())
                    ->setProductId($form->getDataObject()->getId());

                $fieldset->setHeaderBar(
                    $headerBar->toHtml()
                );
            }

            $values = Mage::registry('product')->getData();

            if (!Mage::registry('product')->getId()) {
                foreach ($attributes as $attribute) {
                    if (!isset($values[$attribute->getAttributeCode()])) {
                        $values[$attribute->getAttributeCode()] = $attribute->getDefaultValue();
                    }
                }
            }

            if (Mage::registry('product')->hasLockedAttributes()) {
                foreach (Mage::registry('product')->getLockedAttributes() as $attribute) {
                    if ($element = $form->getElement($attribute)) {
                        $element->setReadonly(true, true);
                    }
                }
            }

            $form->addValues($values);
            $form->setFieldNameSuffix('product');
            $this->setForm($form);
        }

        return parent::_prepareForm();
    }

    /**
     * Render block HTML
     *
     * @return string
     */
    protected function _toHtml()
    {

        $accordion = $this->getLayout()->createBlock('adminhtml/widget_accordion')
            ->setId('packageInfo');

        $accordion->addItem('samples', array(
            'title'   => Mage::helper('adminhtml')->__('Samples'),
            'content' => $this->getLayout()->createBlock('package/adminhtml_catalog_product_edit_tab_package_samples')->toHtml(),
            'open'    => true,
        ));

        $accordion->addItem('links', array(
            'title'   => Mage::helper('adminhtml')->__('Links'),
            'content' => $this->getLayout()->createBlock('package/adminhtml_catalog_product_edit_tab_package_links')->toHtml(),
            'open'    => true,
        ));

        $this->setChild('accordion', $accordion);

        return parent::_toHtml();
    }

    protected function _getAdditionalElementTypes()
    {

        $result = array(
            'price'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_price'),
            'gallery' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_gallery'),
            'image'   => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_image'),
            'boolean' => Mage::getConfig()->getBlockClassName('adminhtml/catalog_product_helper_form_boolean')
        );

        $response = new Varien_Object();
        $response->setTypes(array());

        Mage::dispatchEvent('adminhtml_catalog_product_edit_element_types', array('response'=>$response));

        foreach ($response->getTypes() as $typeName=>$typeClass) {
            $result[$typeName] = $typeClass;
        }

        return $result;
    }
}
