<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * File.class.php - Handle file uploads and downloads.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage util
 * @version    1.0
 */

define ('FILE_TYPE_ERROR', -1);
define ('FILE_SIZE_ERROR', -2);
define ('FILE_SAVE_ERROR', -3);

/**
 * class File
 */
class File
{
	var $errorCode;
	
	function setErrorCode ($errorCode)
	{
		$this->errorCode = $errorCode;
	}
	
	function getErrorCode ()
	{
		return $this->errorCode;
	}

	/**
	 * Check file type and size.
	 *
	 * @param  string File information.
	 * @param  string File type regular expression.
	 * @param  int    Max file size.
	 * @return bool
	 */
	function checkFile ($fileInfo, $typeMatch = NULL, $maxSize = NULL)
	{
		if ($fileInfo['error'] == UPLOAD_ERR_OK)
		{
			if (! is_null($typeMatch))
			{
				if (! ereg($typeMatch, $fileInfo['type']))
				{
					$this->setErrorCode(FILE_TYPE_ERROR);
					return FALSE;
				}
			}

			if (! is_null($maxSize))
			{
				if ($fileInfo['size'] > $maxSize)
				{
					$this->setErrorCode(FILE_SIZE_ERROR);
					return FALSE;
				}
			}

			return TRUE;
		}

		$this->setErrorCode($fileInfo['error']);
		return FALSE;
	}
	
	/**
	 * Save uploaded file in specified directory.
	 *
	 * @param  string File information.
	 * @param  string Filename prefix.
	 * @param  string Destination directory.
	 * @param  octal  File permission mode.
	 * @return bool
	 */
	function saveFile ($fileInfo, $filePath, $chmod = 0777)
	{
		if (! move_uploaded_file ($fileInfo['tmp_name'], $filePath))
		{
			$this->setErrorCode(FILE_SAVE_ERROR);
			return FALSE;
		}

		chmod ($filePath, $chmod);
		return TRUE;
	}

	function downloadFile ($filePath)
	{

	}
}

?>