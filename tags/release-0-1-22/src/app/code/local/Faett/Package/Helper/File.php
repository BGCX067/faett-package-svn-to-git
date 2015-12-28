<?php

/**
 * Faett_Package_Helper_File
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

// include the necessary libraries
require_once 'Faett/Core/Factory.php';

/**
 * Package Products File Helper.
 *
 * @category   	Faett
 * @package    	Faett_Package
 * @copyright  	Copyright (c) 2009 <tw@faett.net> Tim Wagner
 * @license    	<http://www.gnu.org/licenses/> 
 * 				GNU General Public License (GPL 3)
 * @author      Tim Wagner <tw@faett.net>
 */
class Faett_Package_Helper_File extends Mage_Core_Helper_Abstract
{
    /**
     * Checking file for moving and move it
     *
     * @param string $baseTmpPath
     * @param string $basePath
     * @param array $file
     * @return string
     */
    public function moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        if (isset($file[0])) {
            $fileName = $file[0]['file'];
            if ($file[0]['status'] == 'new') {
                try {
                    $fileName = $this->_moveFileFromTmp(
                        $baseTmpPath, $basePath, $file[0]['file']
                    );
                } catch (Exception $e) {
                    Mage::throwException(Mage::helper('package')->__('An error occurred while saving the file(s).'));
                }
            }
            return $fileName;
        }
        return '';
    }

    /**
	 * This method opens and validates the uploaded PEAR package
	 * and assembles the content of the package file itself, the
	 * dependencies and the release notes.
	 *
	 * @param Faett_Package_Model_Link $link The link with the PEAR package file
	 * @return void
     */
    public function generatePEARInfos(Faett_Package_Model_Link $link)
    {
        // initialize the PEAR service implementation
        $service = Faett_Core_Factory::get(Mage::getBaseDir());
        // initialize the PEAR_PackageFile_v2 instance
        $pf = $service->packageFile(
            $packageFile = Faett_Package_Model_Link::getBasePath().$link->getLinkFile()
        );
        // initialize the archive
        $tar = new Archive_Tar($packageFile);
        // try to load the content of the package2.xml file
        $contents = $tar->extractInString(
            'package2.xml'
        );
        // if not available, try to load from package.xml file
        if (!$contents) {
            $contents = $tar->extractInString(
                'package.xml'
            );
        }
        // load the data to assemble the link with
        $link->setPackageFile($contents);
        $link->setPackageName($pf->getName());
        $link->setPackageSize(filesize($packageFile));
        $link->setDependencies(serialize($pf->getDependencies()));
        $link->setState($pf->getState());
        $link->setVersion($pf->getVersion());
        $link->setReleaseDate($pf->getDate());
        $link->setLicence($pf->getLicense());
        $link->setSummary($pf->getSummary());
        $link->setDescription($pf->getDescription());
        $link->setNotes($pf->getNotes());
        // set the licence URI
        if (is_array($loc = $pf->getLicenseLocation())) {
            if (array_key_exists('uri', $loc)) {
                $link->setLicenceUri($loc['uri']);
            } elseif (array_key_exists('filesource', $loc)) {
                $link->setLicenceUri($loc['filesource']);
            }
        }
        // save the completed link
        $link->save();
    }

    /**
     * Move file from tmp path to base path
     *
     * @param string $baseTmpPath
     * @param string $basePath
     * @param string $file
     * @return string
     */
    protected function _moveFileFromTmp($baseTmpPath, $basePath, $file)
    {
        $ioObject = new Varien_Io_File();
        $destDirectory = dirname($this->getFilePath($basePath, $file));
        try {
            $ioObject->open(array('path'=>$destDirectory));
        } catch (Exception $e) {
            $ioObject->mkdir($destDirectory, 0777, true);
            $ioObject->open(array('path'=>$destDirectory));
        }

        if (strrpos($file, '.tmp') == strlen($file)-4) {
            $file = substr($file, 0, strlen($file)-4);
        }

        $destFile = dirname($file) . $ioObject->dirsep()
                  . Varien_File_Uploader::getNewFileName($this->getFilePath($basePath, $file));
        $result = $ioObject->mv(
            $this->getFilePath($baseTmpPath, $file),
            $this->getFilePath($basePath, $destFile)
        );
        return str_replace($ioObject->dirsep(), '/', $destFile);
    }

    /**
     * Return full path to file
     *
     * @param string $path
     * @param string $file
     * @return string
     */
    public function getFilePath($path, $file)
    {
        $file = $this->_prepareFileForPath($file);

        if(substr($file, 0, 1) == DS) {
            return $path . DS . substr($file, 1);
        }

        return $path . DS . $file;
    }

    /**
     * Replace slashes with directory separator
     *
     * @param string $file
     * @return string
     */
    protected function _prepareFileForPath($file)
    {
        return str_replace('/', DS, $file);
    }

    /**
     * Return file name form file path
     *
     * @param string $pathFile
     * @return string
     */
    public function getFileFromPathFile($pathFile)
    {
        $file = '';

        $file = substr($pathFile, strrpos($this->_prepareFileForPath($pathFile), DS)+1);

        return $file;
    }

    public function getFileType($filePath)
    {
        $ext = substr($filePath, strrpos($filePath, '.')+1);
        return $this->_getFileTypeByExt($ext);
    }

    protected function _getFileTypeByExt($ext)
    {
        $type = Mage::getConfig()->getNode('global/mime/types/x' . $ext);
        if ($type) {
            return $type;
        }
        return 'application/octet-stream';
    }
}
