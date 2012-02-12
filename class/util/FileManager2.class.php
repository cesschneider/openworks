<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * FileManager.class.php - Handle file uploads and downloads.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @author     Tiago Baptista 
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

require_once OPENWORKS_CLASS_DIR ."util/ImageResize.class.php";

define ('FILE_MANAGER_TYPE_ERROR', 1);
define ('FILE_MANAGER_SIZE_ERROR', 2);
define ('FILE_MANAGER_DIR_ERROR',  3);

/**
 * class FileManager
 */
class FileManager
{
	var $errorCode;
	var $fileEnd;
	var $fileUploadName;
	
	
	function setErrorCode ($fileId, $errorCode)
	{
		$this->errorCode[$fileId] = $errorCode;
	}
	
	function getErrorCode ($fileId = NULL)
	{
		if (is_null($fileId)) {
			return $this->errorCode;
		} else {
			return $this->errorCode[$fileId];
		}
	}

	/**
	 * Check file type and size.
	 *
	 * @param  string Input file variable name.
	 * @param  string File type regular expression.
	 * @param  int    Max file size.
	 * @return bool
	 */
	function checkFiles ($variable, $matchType = NULL, $maxSize = NULL)
	{
		if (isset($_FILES[$variable]['tmp_name'])) {
			$fileList[0] = $_FILES[$variable];
		} else {
			$fileList = $_FILES[$variable];
		}

		$success = TRUE;

		foreach ($fileList as $fileId => $fileValues)
		{
			if ($fileValues['error'] == 0)
			{
				if (! is_null($matchType))
				{
					if (! ereg($matchType, $fileValues['type']))
					{
						$this->setErrorCode($fileId, FILE_MANAGER_TYPE_ERROR);
						$sucess = FALSE;
					}
				}

				if (! is_null($maxSize))
				{
					if ($fileValues['size'] > $maxSize)
					{
						$this->setErrorCode($fileId, FILE_MANAGER_SIZE_ERROR);
						$sucsess = FALSE;
					}
				}
			}
		}

		return $success;
	}
	
	/**
	 * Save uploaded files in specified directory.
	 *
	 *
	 *
	 *
	 */
	function saveFile ($variable, $prefix, $dir, $overwrite = FALSE)
	{
		
		if (isset($_FILES[$variable]['tmp_name'])) {
			$files[1] = $_FILES[$variable];	
		} else {
			$files = $_FILES[$variable];				
		}

		foreach ($files as $id => $values)
		{
			$filename = $dir . $prefix .'-'. $id;
			
			if ($overwrite == FALSE){
				$check_file_exists = TRUE;
				$file_number = $id;
				
				$d = dir($dir);
								
				while (false !== ($entry = $d->read())){
					if (!is_file($filename)){
						break;		
					} else {
						$file_number++;
						$filename 		   = $dir . $prefix .'-'. $file_number;
					}

				}
			}
			
			
			if (! move_uploaded_file($values['tmp_name'], $filename)) 
			{
				$this->setErrorCode(FILE_MANAGER_DIR_ERROR);
				return FALSE;
			}else{
				$this->fileEnd = $filename;
				$this->fileUploadName = $values['name'];
			}
		}
		
		
		//@chmod($this->fileEnd, 1777);
		return TRUE;		
	}

	function readDir($dir, $restrict_pattern = NULL) 
	{
	   $array = array();
	   $d = dir($dir);
	   
	   $pos = 0;
	   
	   while (false !== ($entry = $d->read())) {
	       if($entry!='.' && $entry!='..') {
			   $filename = $entry;
		       $entry    = $dir.$entry;
	           
	           $file_identify = explode('-', $filename);
	           
	           if ($file_identify[0] == $restrict_pattern or $restrict_pattern === NULL){	           
		           if(is_dir($entry)) {
		               $array = array_merge($array, read_dir($entry.'/'));
		           } else {
					   $array_entry = array_reverse(explode('/', $entry));
					   
		               $array[$pos]['full_path'] 	= $entry;
		               $array[$pos]['reduced_path'] = $array_entry[2] . '/' . $array_entry[1] . '/' . $array_entry[0];
		               $array[$pos]['filename']  	= $filename;
		           }
	           }
	       }
	       $pos++;
	   }
	   $d->close();
	   return $array;
	}	
	
	function resizeImage($width, $height) 
	{
		if(!isset($this->fileEnd) || !trim($this->fileEnd)) 
			return FALSE;
		$image = getimagesize($this->fileEnd);
		if(is_array($image)){
			if(!isset($width) || !intval($width) || !isset($height) || !intval($height) )
				return FALSE;
			if($objResize = ImageResizeFactory::getInstanceOf($this->fileEnd, $this->fileEnd, $width, $height, $image['mime']))
				$objResize->getResizedImage();
		}
		
		
	}
}

?>