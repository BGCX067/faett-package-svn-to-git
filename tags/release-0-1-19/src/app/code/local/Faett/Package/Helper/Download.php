<?php

/**
 * Faett_Package_Helper_Download
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
 * Package Products Download Helper.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Helper_Download extends Mage_Core_Helper_Abstract
{
    const LINK_TYPE_URL         = 'url';
    const LINK_TYPE_FILE        = 'file';

    const XML_PATH_CONTENT_DISPOSITION  = 'catalog/package/content_disposition';

    /**
     * Type of link
     *
     * @var string
     */
    protected $_linkType        = self::LINK_TYPE_FILE;

    /**
     * Resource file
     *
     * @var string
     */
    protected $_resourceFile    = null;

    /**
     * Resource open handle
     *
     * @var resource
     */
    protected $_handle          = null;

    /**
     * Remote server headers
     *
     * @var array
     */
    protected $_urlHeaders      = array();

    /**
     * MIME Content-type for a file
     *
     * @var string
     */
    protected $_contentType     = 'application/octet-stream';

    /**
     * File name
     *
     * @var string
     */
    protected $_fileName        = 'download';

    /**
     * Retrieve Resource file handle (socket, file pointer etc)
     *
     * @return resource
     */
    protected function _getHandle()
    {
        if (!$this->_resourceFile) {
            Mage::throwException(Mage::helper('package')->__('Please set resource file and link type'));
        }

        if (is_null($this->_handle)) {
            if ($this->_linkType == self::LINK_TYPE_URL) {
                $port = 80;

                /**
                 * Validate URL
                 */
                $urlProp = parse_url($this->_resourceFile);
                if (!isset($urlProp['scheme']) || strtolower($urlProp['scheme'] != 'http')) {
                    Mage::throwException(Mage::helper('package')->__('Invalid download URL scheme'));
                }
                if (!isset($urlProp['host'])) {
                    Mage::throwException(Mage::helper('package')->__('Invalid download URL host'));
                }
                $hostname = $urlProp['host'];

                if (isset($urlProp['port'])) {
                    $port = (int)$urlProp['port'];
                }

                $path = '/';
                if (isset($urlProp['path'])) {
                    $path = $urlProp['path'];
                }
                $query = '';
                if (isset($urlProp['query'])) {
                    $query = '?' . $urlProp['query'];
                }

                try {
                    $this->_handle = fsockopen($hostname, $port, $errno, $errstr);
                }
                catch (Exception $e) {
                    throw $e;
                }

                if ($this->_handle === false) {
                    Mage::throwException(Mage::helper('package')->__('Can\'t connect to remote host, error: %s', $errstr));
                }

                $headers = 'GET ' . $path . $query . ' HTTP/1.0' . "\r\n"
                    . 'Host: ' . $hostname . "\r\n"
                    . 'User-Agent: Magento ver/' . Mage::getVersion() . "\r\n"
                    . 'Connection: close' . "\r\n"
                    . "\r\n";
                fwrite($this->_handle, $headers);

                while (!feof($this->_handle)) {
                    $str = fgets($this->_handle, 1024);
                    if ($str == "\r\n") {
                        break;
                    }
                    $match = array();
                    if (preg_match('#^([^:]+): (.*)\s+$#', $str, $match)) {
                        $k = strtolower($match[1]);
                        if ($k == 'set-cookie') {
                            continue;
                        }
                        else {
                            $this->_urlHeaders[$k] = trim($match[2]);
                        }
                    }
                    elseif (preg_match('#^HTTP/[0-9\.]+ (\d+) (.*)\s$#', $str, $match)) {
                        $this->_urlHeaders['code'] = $match[1];
                        $this->_urlHeaders['code-string'] = trim($match[2]);
                    }
                }

                if (!isset($this->_urlHeaders['code']) || $this->_urlHeaders['code'] != 200) {
                    Mage::throwException(Mage::helper('package')->__('Sorry, the was an error getting requested content. Please contact store owner.'));
                }
            }
            elseif ($this->_linkType == self::LINK_TYPE_FILE) {
                $this->_handle = new Varien_Io_File();
                $this->_handle->open(array('path'=>Mage::getBaseDir('var')));
                if (!$this->_handle->fileExists($this->_resourceFile, true)) {
                    Mage::throwException(Mage::helper('package')->__('File does not exists'));
                }
                $this->_handle->streamOpen($this->_resourceFile, 'r');
            }
            else {
                Mage::throwException(Mage::helper('package')->__('Invalid download link type'));
            }
        }
        return $this->_handle;
    }

    /**
     * Retrieve file size in bytes
     */
    public function getFilesize()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            return $handle->streamStat('size');
        }
        elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-length'])) {
                return $this->_urlHeaders['content-length'];
            }
        }
        return null;
    }

    public function getContentType()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            if (function_exists('mime_content_type')) {
                return mime_content_type($this->_resourceFile);
            } else {
                return Mage::helper('package/file')->getFileType($this->_resourceFile);
            }
        }
        elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-type'])) {
                $contentType = split('; ', $this->_urlHeaders['content-type']);
                return $contentType[0];
            }
        }
        return $this->_contentType;
    }

    public function getFilename()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            return pathinfo($this->_resourceFile, PATHINFO_BASENAME);
        }
        elseif ($this->_linkType == self::LINK_TYPE_URL) {
            if (isset($this->_urlHeaders['content-disposition'])) {
                $contentDisposition = split('; ', $this->_urlHeaders['content-disposition']);
                if (!empty($contentDisposition[1]) && strpos($contentDisposition[1], 'filename=') !== false) {
                    return substr($contentDisposition[1], 9);
                }
            }
            if ($fileName = @pathinfo($this->_resourceFile, PATHINFO_BASENAME)) {
                return $fileName;
            }
        }
        return $this->_fileName;
    }

    /**
     * Set resource file for download
     *
     * @param string $resourceFile
     * @param string $linkType
     * @return Faett_Package_Helper_Download
     */
    public function setResource($resourceFile, $linkType = self::LINK_TYPE_FILE)
    {
        $this->_resourceFile    = $resourceFile;
        $this->_linkType        = $linkType;

        return $this;
    }

    /**
     * Retrieve Http Request Object
     *
     * @return Mage_Core_Controller_Request_Http
     */
    public function getHttpRequest()
    {
        return Mage::app()->getFrontController()->getRequest();
    }

    /**
     * Retrieve Http Response Object
     *
     * @return Mage_Core_Controller_Response_Http
     */
    public function getHttpResponse()
    {
        return Mage::app()->getFrontController()->getResponse();
    }

    public function output()
    {
        $handle = $this->_getHandle();
        if ($this->_linkType == self::LINK_TYPE_FILE) {
            while ($buffer = $handle->streamRead()) {
                print $buffer;
            }
        }
        elseif ($this->_linkType == self::LINK_TYPE_URL) {
            while (!feof($handle)) {
                print fgets($handle, 1024);
            }
        }
    }

    /**
     * Use Content-Disposition: attachment
     *
     * @param mixed $store
     * @return bool
     */
    public function getContentDisposition($store = null)
    {
        return Mage::getStoreConfig(self::XML_PATH_CONTENT_DISPOSITION, $store);
    }
}
