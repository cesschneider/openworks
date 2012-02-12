<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * common.lib.php - Common functions used by some classes.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage core
 * @version 1.0
 */

/**
 * Dump array values and print/return contents.
 *
 * If PHP parameter 'html_errors' is set,
 * format output with HTML tags.
 *
 * @param  mixed  Variable to be dumped
 * @param  bool   Flag that says to print or return variable values.
 * @param  string Function used to dump variable.
 * @param  bool   Dump variable with HTML characters or not.
 * @return mixed  Void if $print is set to FALSE or string if set to TRUE.
 */
function dump ($variable, $print_flag = NULL, $dump_function = NULL, $html_errors = NULL)
{
	Message::notice('Deprecated function. Use Util::dump() or Util::htmlDump()', '', 1);
	
	if (is_null($print_flag)) {
		$print_flag = TRUE;
	}
	if (is_null($dump_function)) {
		$dump_function = 'print_r';
	}
	if (is_null($html_errors)) {
		$html_errors = ini_get('html_errors');
	}

	ob_start();
	$dump_function($variable);
	$content = ob_get_contents() ."\n";
	ob_end_clean();

	if ($html_errors)
	{
		$content = str_replace('<br />','<br/>', nl2br($content));
		$content = str_replace(chr(32),'&nbsp;', $content);
	}

	if ($print_flag) {
		print $content;
	} else {
		return $content;
	}
}

function html_dump ($variable, $label = NULL)
{
	print "<!--\n";

	if (! is_null($label)) {
		print "$label ";
	}

	dump($variable, NULL, NULL, FALSE);
	print "-->\n";
}

?>