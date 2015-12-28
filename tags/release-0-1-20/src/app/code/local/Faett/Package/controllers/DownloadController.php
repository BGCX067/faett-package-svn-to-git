<?php

/**
 * Faett_Package_DownloadController
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
 * Download controller
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_DownloadController extends Mage_Core_Controller_Front_Action
{

    /**
     * Return core session object
     *
     * @return Mage_Core_Model_Session
     */
    protected function _getSession()
    {
        return Mage::getSingleton('core/session');
    }

    /**
     * Return customer session object
     *
     * @return Mage_Customer_Model_Session
     */
    protected function _getCustomerSession()
    {
        return Mage::getSingleton('customer/session');
    }

    protected function _processDownload($resource, $resourceType)
    {
        $helper = Mage::helper('package/download');
        /* @var $helper Faett_Package_Helper_Download */

        $helper->setResource($resource, $resourceType);

        $fileName       = $helper->getFilename();
        $contentType    = $helper->getContentType();

        $this->getResponse()
            ->setHttpResponseCode(200)
            ->setHeader('Pragma', 'public', true)
            ->setHeader('Cache-Control', 'must-revalidate, post-check=0, pre-check=0', true)
            ->setHeader('Content-type', $contentType, true);

        if ($fileSize = $helper->getFilesize()) {
            $this->getResponse()
                ->setHeader('Content-Length', $fileSize);
        }

        if ($contentDisposition = $helper->getContentDisposition()) {
            $this->getResponse()
                ->setHeader('Content-Disposition', $contentDisposition . '; filename='.$fileName);
        }

        $this->getResponse()
            ->clearBody();
        $this->getResponse()
            ->sendHeaders();

        $helper->output();
    }

    /**
     * Download sample action
     *
     */
    public function sampleAction()
    {
        $sampleId = $this->getRequest()->getParam('sample_id', 0);
        $sample = Mage::getModel('package/sample')->load($sampleId);
        if ($sample->getId()) {
            $resource = '';
            $resourceType = '';
            if ($sample->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_URL) {
                $resource = $sample->getSampleUrl();
                $resourceType = Faett_Package_Helper_Download::LINK_TYPE_URL;
            } elseif ($sample->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('package/file')->getFilePath(
                    Faett_Package_Model_Sample::getBasePath(), $sample->getSampleFile()
                );
                $resourceType = Faett_Package_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Mage_Core_Exception $e) {
                $this->_getSession()->addError(Mage::helper('package')->__('Sorry, there was an error getting requested content. Please contact store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link's sample action
     *
     */
    public function linkSampleAction()
    {
        $linkId = $this->getRequest()->getParam('link_id', 0);
        $link = Mage::getModel('package/link')->load($linkId);
        if ($link->getId()) {
            $resource = '';
            $resourceType = '';
            if ($link->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_URL) {
                $resource = $link->getSampleUrl();
                $resourceType = Faett_Package_Helper_Download::LINK_TYPE_URL;
            } elseif ($link->getSampleType() == Faett_Package_Helper_Download::LINK_TYPE_FILE) {
                $resource = Mage::helper('package/file')->getFilePath(
                    Faett_Package_Model_Link::getBaseSamplePath(), $link->getSampleFile()
                );
                $resourceType = Faett_Package_Helper_Download::LINK_TYPE_FILE;
            }
            try {
                $this->_processDownload($resource, $resourceType);
                exit(0);
            } catch (Mage_Core_Exception $e) {
                $this->_getCustomerSession()->addError(Mage::helper('package')->__('Sorry, there was an error getting requested content. Please contact store owner.'));
            }
        }
        return $this->_redirectReferer();
    }

    /**
     * Download link action.
     *
     * @return void
     */
    public function linkAction()
    {
        // load the serialz from the request
        $id = $this->getRequest()->getParam('id', 0);
        // load the purchased link
        $linkPurchased = Mage::getModel('package/link_purchased')->load($id, 'serialz');
        // check if a valid link was found
        if (!$linkPurchased->getId() ) {
            $this->_getCustomerSession()->addNotice(
            	Mage::helper('package')->__("Requested link doesn't exist.")
            );
            return $this->_redirect('*/*/index');
        }
        // load the customer and the product ID
        $customerId = $this->_getCustomerSession()->getCustomerId();
        $productId = $linkPurchased->getProductId();
        // check if a valid customer id was found
        if (!$customerId) {
            // if not load the product
            $product = Mage::getModel('catalog/product')->load($productId);
            // load the product ID
            if ($product->getId()) {
                $notice = Mage::helper('package')->__(
                    'Please log in to download your product or purchase <a href="%s">%s</a>.',
                    $product->getProductUrl(), $product->getName()
                );
            } else {
                $notice = Mage::helper('package')->__('Please log in to download your product.');
            }
            // log the notice
            $this->_getCustomerSession()->addNotice($notice);
            // redirect to the index page
            return $this->_redirect('*/*/index');
        }
        // check if the customer ID's are equal, means the customer has bought the package
        if ($linkPurchased->getCustomerId() != $customerId) {
            $this->_getCustomerSession()->addNotice(
            	Mage::helper('package')->__("Requested link doesn't exist.")
            );
            return $this->_redirect('*/*/index');
        }
        // check the package state
        if ($linkPurchased->getState() == Faett_Package_Model_Link_Purchased::LINK_STATE_AVAILABLE) {
            // initialize the resource
            $resource = '';
            $resourceType = '';
            // load the package
            $product = Mage::getModel('catalog/product')->load($productId);
            // load the available links
            $links = $product->getTypeInstance(true)->getLinks($product);
            // check the serialz
            $serialz = Mage::getModel('package/serialz')
                ->init($linkPurchased)
                ->decrypt($linkPurchased->getSerialz());
            // iterate over the links and check the customers serialz
            foreach ($links as $link) {
                // if the serialz is valid for the link
                if ($link->getReleaseDate() <= $serialz->getValidThru()) {
                    // load the resource
                    $resource = Mage::helper('package/file')->getFilePath(
                        Faett_Package_Model_Link::getBasePath(), $link->getLinkFile()
                    );
                    // set the resource type
                    $resourceType = Faett_Package_Helper_Download::LINK_TYPE_FILE;
                    try {
                        // process the download
                        $this->_processDownload($resource, $resourceType);
                        exit(0);
                    } catch (Exception $e) {
                    	
                    	Mage::logException($e);
                    	
                        $this->_getCustomerSession()->addNotice(
                            Mage::helper('package')->__(
                            	'Sorry, there was an error getting requested content. Please contact store owner.'
                            )
                        );
                    }
                }
            }
        } elseif ($linkPurchased->getState() == Faett_Package_Model_Link_Purchased::LINK_STATE_EXPIRED) {
            $this->_getCustomerSession()->addNotice(Mage::helper('package')->__('Link has expired.'));
        } elseif ($linkPurchased->getState() == Faett_Package_Model_Link_Purchased::LINK_STATE_PENDING) {
        	$this->_getCustomerSession()->addNotice(Mage::helper('package')->__('Link is not available.'));
        } else {
        	$this->_getCustomerSession()->addNotice(
                Mage::helper('package')->__('Sorry, there was an error getting requested content. Please contact store owner.')
            );
        }
        // return to the main page
        return $this->_redirect('*/customer/products');
    }
}