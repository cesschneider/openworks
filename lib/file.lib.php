<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * file.lib.php - File functions.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage core
 * @version 1.0
 */

if (! function_exists('file_put_contents'))
{
	function file_put_contents ($filename, $content)
	{
		$file = fopen($filename, 'w');
		fwrite($file, $content);
		fclose($file);
	}
}

?>