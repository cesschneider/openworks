<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * FileManager.class.php - Handle file uploads.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

define ('FILE_MANAGER_TYPE_ERROR', 1);
define ('FILE_MANAGER_SIZE_ERROR', 2);
define ('FILE_MANAGER_DIR_ERROR',  3);

/**
 * class FileManager
 */
class FileManager
{
	var $errorCode;
	
	function setErrorCode ($code)
	{
		$this->errorCode = $code;	
	}
	
	function getErrorCode ()
	{
		return $this->errorCode;	
	}

	function checkFile ($variable, $matchType = NULL, $maxSize = NULL)
	{
		if (isset($_FILES[$variable]['tmp_name'])) {
			$files[1] = $_FILES[$variable];	
		} else {
			$files = $_FILES[$variable];				
		}

		foreach ($files as $id => $values)
		{
			if ($values['error'] == 0)
			{
				if ($matchType !== NULL){
					if (! ereg($matchType, $values['type'])) 
					{
						$this->setErrorCode(FILE_MANAGER_TYPE_ERROR);
						return FALSE;	
					}
				}

				if ($matchType !== NULL){
					if ($values['size'] > $maxSize) 
					{
						$this->setErrorCode(FILE_MANAGER_SIZE_ERROR);
						return FALSE;	
					}	
				}
			}
		}

		return TRUE;
	}	
	
	function storeFile ($variable, $prefix, $dir, $overwrite = FALSE)
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
			}
		}
		
		return TRUE;		
	}

	function readDir($dir, $restrict_pattern = NULL) {
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
		               $array[$pos]['full_path'] = $entry;
		               $array[$pos]['filename']  = $filename;
		           }
	           }
	       }
	       $pos++;
	   }
	   $d->close();
	   return $array;
	}	
	
}

?>