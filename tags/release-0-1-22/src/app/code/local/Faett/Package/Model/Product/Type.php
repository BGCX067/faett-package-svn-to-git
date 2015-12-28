<?php

/**
 * Faett_Package_Model_Product_Type
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
 * Package product type model.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Model_Product_Type
    extends Mage_Catalog_Model_Product_Type_Virtual {

    const TYPE_PACKAGE = 'package';

    public function isPackage()
    {
        return true;
    }

    /**
     * Get package product links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getLinks($product = null)
    {
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        if (is_null($product->getPackageLinks())) {
            $_linkCollection = Mage::getModel('package/link')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId())
                ->addPriceToResult($product->getStore()->getWebsiteId())
                ->addOrder('link_id', 'DESC');
            $linksCollectionById = array();
            foreach ($_linkCollection as $link) {
                $link->setProduct($product);
                $linksCollectionById[$link->getId()] = $link;
            }
            $product->setPackageLinks($linksCollectionById);
        }
        return $product->getPackageLinks();
    }

    /**
     * Check if product has links
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasLinks($product = null)
    {
        return count($this->getLinks($product)) > 0;
    }

    /**
     * Check if product has options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasOptions($product = null)
    {
        //return true;
        return $this->getProduct($product)->getLinksPurchasedSeparately()
            || parent::hasOptions($product);
    }

    /**
     * Check if product has required options
     *
     * @param Mage_Catalog_Model_Product $product
     * @return bool
     */
    public function hasRequiredOptions($product = null)
    {
        if (parent::hasRequiredOptions($product) || $this->getProduct($product)->getLinksPurchasedSeparately()) {
            return true;
        }
        return false;
    }

    /**
     * Check if product cannot be purchased with no links selected
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function getLinkSelectionRequired($product = null)
    {
        return $this->getProduct($product)->getLinksPurchasedSeparately();
    }

    /**
     * Get package product samples
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Faett_Package_Model_Mysql4_Sample_Collection
     */
    public function getSamples($product = null)
    {
        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */
        if (is_null($product->getPackageSamples())) {
            $_sampleCollection = Mage::getModel('package/sample')->getCollection()
                ->addProductToFilter($product->getId())
                ->addTitleToResult($product->getStoreId());
            $product->setPackageSamples($_sampleCollection);
        }

        return $product->getPackageSamples();
    }

    /**
     * Check if product has samples
     *
     * @param Mage_Catalog_Model_Product $product
     * @return boolean
     */
    public function hasSamples($product = null)
    {
        return count($this->getSamples($product)) > 0;
    }

    /**
     * Enter description here...
     *
     * @param Mage_Catalog_Model_Product $product
     * @return Faett_Package_Model_Product_Type
     */
    public function save($product = null)
    {
        parent::save($product);

        $product = $this->getProduct($product);
        /* @var Mage_Catalog_Model_Product $product */

        if ($data = $product->getPackageData()) {
            if (isset($data['sample'])) {
                $_deleteItems = array();
                foreach ($data['sample'] as $sampleItem) {
                    if ($sampleItem['is_delete'] == '1') {
                        if ($sampleItem['sample_id']) {
                            $_deleteItems[] = $sampleItem['sample_id'];
                        }
                    } else {
                        unset($sampleItem['is_delete']);
                        if (!$sampleItem['sample_id']) {
                            unset($sampleItem['sample_id']);
                        }
                        $sampleModel = Mage::getModel('package/sample');
                        $files = array();
                        if (isset($sampleItem['file'])) {
                            $files = Zend_Json::decode($sampleItem['file']);
                            unset($sampleItem['file']);
                        }

                        $sampleModel->setData($sampleItem)
                            ->setSampleType($sampleItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId());

                        if ($sampleModel->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_FILE) {
                            $sampleFileName = Mage::helper('package/file')->moveFileFromTmp(
                                Faett_Package_Model_Sample::getBaseTmpPath(),
                                Faett_Package_Model_Sample::getBasePath(),
                                $files
                            );
                            $sampleModel->setSampleFile($sampleFileName);
                        }
                        $sampleModel->save();
                    }
                }
                if ($_deleteItems) {
                    Mage::getResourceModel('package/sample')->deleteItems($_deleteItems);
                }
            }
            if (isset($data['link'])) {
                $_deleteItems = array();
                foreach ($data['link'] as $linkItem) {
                    if ($linkItem['is_delete'] == '1') {
                        if ($linkItem['link_id']) {
                            $_deleteItems[] = $linkItem['link_id'];
                        }
                    } else {
                        unset($linkItem['is_delete']);
                        if (!$linkItem['link_id']) {
                            unset($linkItem['link_id']);
                        }
                        $files = array();
                        if (isset($linkItem['file'])) {
                            $files = Zend_Json::decode($linkItem['file']);
                            unset($linkItem['file']);
                        }
                        $sample = array();
                        if (isset($linkItem['sample'])) {
                            $sample = $linkItem['sample'];
                            unset($linkItem['sample']);
                        }
                        $linkModel = Mage::getModel('package/link')
                            ->setData($linkItem)
                            ->setLinkType($linkItem['type'])
                            ->setProductId($product->getId())
                            ->setStoreId($product->getStoreId())
                            ->setWebsiteId($product->getStore()->getWebsiteId());
                        if (null === $linkModel->getPrice()) {
                            $linkModel->setPrice(0);
                        }
                        if ($linkModel->getIsUnlimited()) {
                            $linkModel->setNumberOfDownloads(0);
                        }
                        $sampleFile = array();
                        if ($sample && isset($sample['type'])) {
                            if ($sample['type'] == 'url' && $sample['url'] != '') {
                                $linkModel->setSampleUrl($sample['url']);
                            }
                            $linkModel->setSampleType($sample['type']);
                            $sampleFile = Zend_Json::decode($sample['file']);
                        }
                        if ($linkModel->getLinkType() == Faett_Package_Helper_Download::LINK_TYPE_FILE) {
                            $linkFileName = Mage::helper('package/file')->moveFileFromTmp(
                                Faett_Package_Model_Link::getBaseTmpPath(),
                                Faett_Package_Model_Link::getBasePath(),
                                $files
                            );
                            $linkModel->setLinkFile($linkFileName);
                        }
                        if ($linkModel->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_FILE) {
                            $linkSampleFileName = Mage::helper('package/file')->moveFileFromTmp(
                                Faett_Package_Model_Link::getBaseSampleTmpPath(),
                                Faett_Package_Model_Link::getBaseSamplePath(),
                                $sampleFile
                            );
                            $linkModel->setSampleFile($linkSampleFileName);
                        }

                        $linkModel->save();

                        Mage::helper('package/file')->generatePEARInfos(
                            $linkModel
                        );
                    }
                }
                if ($_deleteItems) {
                    Mage::getResourceModel('package/link')->deleteItems($_deleteItems);
                }
            }
        }

        return $this;
    }

    /**
     * Enter description here...
     *
     * @param Varien_Object $buyRequest
     * @param Mage_Catalog_Model_Product $product
     * @return array|string
     */
    public function prepareForCart(Varien_Object $buyRequest, $product = null)
    {
        $result = parent::prepareForCart($buyRequest, $product);

        if (is_string($result)) {
            return $result;
        }
        // if adding product from admin area we add all links to product
        if ($this->getProduct($product)->getSkipCheckRequiredOption()) {
            $this->getProduct($product)->setLinksPurchasedSeparately(false);
        }
        $preparedLinks = array();
        if ($this->getProduct($product)->getLinksPurchasedSeparately()) {
            if ($links = $buyRequest->getLinks()) {
                foreach ($this->getLinks($product) as $link) {
                    if (in_array($link->getId(), $links)) {
                        $preparedLinks[] = $link->getId();
                    }
                }
            }
        } else {
            foreach ($this->getLinks($product) as $link) {
                $preparedLinks[] = $link->getId();
            }
        }
        if ($preparedLinks) {
            $this->getProduct($product)->addCustomOption('package_link_ids', implode(',', $preparedLinks));
            return $result;
        }
        if ($this->getLinkSelectionRequired($product)) {
            return Mage::helper('package')->__('Please specify product link(s).');
        }
        return $result;
    }

    /**
     * Prepare additional options/information for order item which will be
     * created from this product
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getOrderOptions($product = null)
    {
        $options = parent::getOrderOptions($product);
        if ($linkIds = $this->getProduct($product)->getCustomOption('package_link_ids')) {
            $linkOptions = array();
            $links = $this->getLinks($product);
            foreach (explode(',', $linkIds->getValue()) as $linkId) {
                if (isset($links[$linkId])) {
                    $linkOptions[] = $linkId;
                }
            }
            $options = array_merge($options, array('links' => $linkOptions));
        }
        $options = array_merge($options, array(
            'is_package' => true,
            'real_product_type' => self::TYPE_PACKAGE
        ));
        return $options;
    }



    /**
     * Setting flag if dowenloadable product can be or not in complex product
     * based on link can be purchased separately or not
     *
     * @param Mage_Catalog_Model_Product $product
     */
    public function beforeSave($product = null)
    {
        parent::beforeSave($product);


        if ($this->getLinkSelectionRequired($product)) {
            $this->getProduct($product)->setTypeHasOptions(true);
            $this->getProduct($product)->setTypeHasRequiredOptions(true);
        } else {
            $this->getProduct($product)->setTypeHasOptions(false);
            $this->getProduct($product)->setTypeHasRequiredOptions(false);
        }
    }

    /**
     * Retrieve additional searchable data from type instance
     * Using based on product id and store_id data
     *
     * @param Mage_Catalog_Model_Product $product
     * @return array
     */
    public function getSearchableData($product = null)
    {
        $searchData = parent::getSearchableData($product);
        $product = $this->getProduct($product);

        $linkSearchData = Mage::getSingleton('package/link')
            ->getSearchableData($product->getId(), $product->getStoreId());
        if ($linkSearchData) {
            $searchData = array_merge($searchData, $linkSearchData);
        }

        $sampleSearchData = Mage::getSingleton('package/sample')
            ->getSearchableData($product->getId(), $product->getStoreId());
        if ($sampleSearchData) {
            $searchData = array_merge($searchData, $sampleSearchData);
        }

        return $searchData;
    }
}
