<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Image.class.php - Handle image files.
 *
 * @author     Cesar Schneider <cesschneider@gmail.com>
 * @package    openworks
 * @subpackage util
 * @version    1.0
 */

define('IMAGE_ORIENTATION_VERTICAL',    1);
define('IMAGE_ORIENTATION_HORIZONTAL',  2);
define('IMAGE_ORIENTATION_RECTANGULAR', 3);

/**
 * class Image
 *
 * @package openworks
 */
class Image
{
	var $sourceFile;
	var $sourceType;
	var $sourceWidth;
	var $sourceHeight;
	var $sourceRatio;
	var $sourceWidthRatio;
	var $sourceOrientation;

	var $destinationImage;
	var $destinationQuality;

	var $mimeTypes;
	var $debug;

	function Image ($sourceFile = NULL, $sourceType = NULL)
	{
		$this->mimeTypes = array (
			'.gif'  => 'image/gif',
			'.png'  => 'image/png',
			'.jpg'  => 'image/jpeg',
			'.jpe'  => 'image/jpeg',
			'.jpeg' => 'image/jpeg'
		);

		$this->destinationQuality = 75;

		if (! is_null($sourceFile)) {
			$this->setSourceFile ($sourceFile, $sourceType);
		}

		$this->debug = FALSE;
	}

	function setDestinationQuality ($quality)
	{
		$this->destinationQuality = $quality;
	}

	function setSourceFile ($sourceFile, $sourceType = NULL)
	{
		$this->sourceFile = $sourceFile;

		// get current sizes and content type]
		$image = getimagesize ($this->sourceFile);

		list ($this->sourceWidth, $this->sourceHeight) = $image;

		$this->sourceType = $image['mime'];

		$this->sourceRatio = ($this->sourceWidth < $this->sourceHeight) ?
								($this->sourceWidth / $this->sourceHeight) :
								($this->sourceHeight / $this->sourceWidth);

		$this->sourceWidthRatio = $this->sourceWidth / $this->sourceHeight;

		if ($this->sourceWidth < $this->sourceHeight) {
			$this->sourceOrientation = IMAGE_ORIENTATION_VERTICAL;
		} else if ($this->sourceWidth > $this->sourceHeight) {
			$this->sourceOrientation = IMAGE_ORIENTATION_HORIZONTAL;
		} else {
			$this->sourceOrientation = IMAGE_ORIENTATION_RECTANGULAR;
		}

		if ($this->debug)
		{
			trigger_error('Source file name: '. $this->sourceFile);
			trigger_error('Source file size: '.
				$this->sourceWidth . ' x '. $this->sourceHeight .' ('. $this->sourceRatio .')');
			trigger_error('Source file type: '. $this->sourceType . ' '. $this->getSourceOrientation());
		}
	}

	function getSourceRatio ()
	{
		return $this->sourceRatio;
	}

	function getSourceOrientation ()
	{
		switch ($this->sourceOrientation)
		{
			case IMAGE_ORIENTATION_VERTICAL:
				return 'vert';
			case IMAGE_ORIENTATION_HORIZONTAL:
				return 'horiz';
			case IMAGE_ORIENTATION_RECTANGULAR:
				return 'rect';
		}
	}

	function resizeImage ($maxWidth = NULL, $maxHeight = NULL, $resizeRatio = 1)
	{
		if ($this->debug) {
			trigger_error('Resizing image using parameters: '.
							$maxWidth .' '. $maxHeight .' '. $resizeRatio);
		}

		$newWidth  = $this->sourceWidth  * $resizeRatio;
		$newHeight = $this->sourceHeight * $resizeRatio;

		if (! is_null($maxWidth) && $newWidth > $maxWidth)
		{
			$newWidth  = $maxWidth;
			$newHeight = round($maxWidth / $this->sourceWidthRatio);

			if ($newHeight > $maxHeight)
			{
				$newHeight = $maxHeight;
				$newWidth = round($maxHeight * $this->sourceWidthRatio);
			}
		}
		if (! is_null($maxHeight) && $newHeight > $maxHeight)
		{
			$newHeight = $maxHeight;
			$newWidth  = round($maxHeight * $this->sourceWidthRatio);

			if ($newWidth > $maxWidth)
			{
				$newWidth = $maxWidth;
				$newHeight = round($maxWidth / $this->sourceRatio);
			}
		}

		$sourceImage = &$this->getSourceImage();

		if ($sourceImage === FALSE)
		{
			trigger_error('Invalid source image', E_USER_WARNING);
			return FALSE;
		}

		if ($this->debug) {
			trigger_error('New image size: '. $newWidth .' x '. $newHeight);
		}

		$this->destinationImage = imagecreatetruecolor ($newWidth, $newHeight);

//		$index = imagecolorat($sourceImage, 0, 0);
//		$colors = imagecolorsforindex($sourceImage, $index);
//		imagecolorset($this->destinationImage, $index, $colors['red'], $colors['green'], $colors['blue']);

		imagecopyresampled ($this->destinationImage, $sourceImage, 0, 0, 0, 0,
						  $newWidth, $newHeight, $this->sourceWidth, $this->sourceHeight);
	}

	function cropImage ($cropRatio = 0.75)
	{
		if ($this->debug) {
			trigger_error('Cropping image using ratio: '. $cropRatio);
		}

		$newWidth  = $this->sourceWidth;
		$newHeight = $this->sourceHeight;
		$dstX = $dstY = $srcX = $srcY = 0;

		if ($this->sourceOrientation == IMAGE_ORIENTATION_HORIZONTAL)
		{
			if (is_array($cropRatio))
			{
				$newWidth = $cropRatio[0];
				$newHeight = $cropRatio[1];
				$srcY = ($this->sourceHeight - $newHeight) / 2;
			}
			else {
				$newWidth = round($this->sourceHeight / $cropRatio);
			}

			$srcX = ($this->sourceWidth - $newWidth) / 2;
		}
		else if ($this->sourceOrientation == IMAGE_ORIENTATION_VERTICAL)
		{
			if (is_array($cropRatio))
			{
				$newWidth = $cropRatio[0];
				$newHeight = $cropRatio[1];
				$srcX = ($this->sourceWidth - $newWidth) / 2;
			}
			else {
				$newHeight = round($this->sourceWidth / $cropRatio);
			}

			$srcY = ($this->sourceHeight - $newHeight) / 2;
		}

		$sourceImage = &$this->getSourceImage();

		if ($sourceImage === FALSE)
		{
			trigger_error('Invalid source image', E_USER_WARNING);
			return FALSE;
		}

		if ($this->debug) {
			trigger_error('New image size: '. $newWidth .' x '. $newHeight);
		}

		$this->destinationImage = imagecreatetruecolor ($newWidth, $newHeight);

		imagecopy ($this->destinationImage, $sourceImage, $dstX, $dstY, $srcX, $srcY,
					$this->sourceWidth, $this->sourceHeight);
	}

	function &getSourceImage ()
	{
		switch ($this->sourceType)
		{
			case 'image/jpeg':
				return imagecreatefromjpeg ($this->sourceFile);

			case 'image/gif':
				return imagecreatefromgif ($this->sourceFile);

			case 'image/png':
				return imagecreatefrompng ($this->sourceFile);

			default:
				return FALSE;
		}
	}

	function createFile ($destinationFile, $destinationType = NULL, $chmod = 0777)
	{
		if (is_null($destinationType)) {
			$destinationType = $this->sourceType;
		}

		if (is_null($this->destinationImage))
		{
			trigger_error('Invalid destination image', E_USER_WARNING);
			return FALSE;
		}

		switch ($destinationType)
		{
			case 'image/jpeg':
				imageinterlace ($this->destinationImage, 1);
				$return = imagejpeg ($this->destinationImage, $destinationFile, $this->destinationQuality);
				break;

			case 'image/gif':
				$return = imagegif ($this->destinationImage, $destinationFile);
				break;

			case 'image/png':
				$return = imagepng ($this->destinationImage, $destinationFile);
				break;

			default:
				return FALSE;
		}

		chmod($destinationFile, $chmod);
		return $return;
	}

}

?>