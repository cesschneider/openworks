<?php

//-------------------------------------------------------------------------
// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.
//-------------------------------------------------------------------------

/**
 * Message.class.php - Handle PHP and application messages.
 *
 * Provides methods to handle PHP and application messages with a set of
 * features that can help developers to deploy their codes.
 *
 * @author     Cesar Schneider <cesschneider at users dot sf dot net>
 * @package    openworks
 * @subpackage core
 * @since      1.0
 * @version    1.0
 */

define('MESSAGE_PHP_ERROR',     E_ERROR);                    // 0000 0000 0001 (1)
define('MESSAGE_PHP_WARNING',   E_WARNING);                  // 0000 0000 0010 (2)
define('MESSAGE_PHP_PARSE',     E_PARSE);                    // 0000 0000 0100 (4)
define('MESSAGE_PHP_NOTICE',    E_NOTICE);                   // 0000 0000 1000 (8)
define('MESSAGE_PHP_DEBUGGING', 15);                         // 0000 0000 1111

define('MESSAGE_PHP_USER_ERROR',   E_USER_ERROR);            // 0001 0000 0000 (256)
define('MESSAGE_PHP_USER_WARNING', E_USER_WARNING);          // 0010 0000 0000 (512)
define('MESSAGE_PHP_USER_NOTICE',  E_USER_NOTICE);           // 0100 0000 0000 (1024)
define('MESSAGE_PHP_ALL',          E_ALL);                   // 0111 1111 1111 (2047)
define('MESSAGE_PHP_STRICT',       2048);                    // 1000 0000 0000

define('MESSAGE_APP_ERROR',      4096);                 // 0001 0000 0000 0000
define('MESSAGE_APP_WARNING',    8192);                 // 0010 0000 0000 0000
define('MESSAGE_APP_EMERGENCY', 12288);                 // 0011 0000 0000 0000

define('MESSAGE_APP_ALERT',        16384);              // 0100 0000 0000 0000
define('MESSAGE_APP_NOTICE',       32768);              // 1000 0000 0000 0000
define('MESSAGE_APP_NOTIFICATION', 49152);              // 1100 0000 0000 0000

define('MESSAGE_APP_INFO',        65536);          // 0001 0000 0000 0000 0000
define('MESSAGE_APP_DEBUG',      131072);          // 0010 0000 0000 0000 0000
define('MESSAGE_APP_DEBUGGING',  196608);          // 0011 0000 0000 0000 0000

define('MESSAGE_APP_ALL',   258048);               // 0011 1111 0000 0000 0000

define('MESSAGE_ALL',        260095);              // 0011 1111 0111 1111 1111
define('MESSAGE_ALL_STRICT', 262143);              // 0011 1111 1111 1111 1111


if (! defined('MESSAGE_DEBUG')) {
	define('MESSAGE_DEBUG', FALSE);
}

/**
 * @package openworks
 */
class Message
{

	/**
	 * Constructor
	 *
	 * Do nothing because this class is generaly used statically.
	 */
	function Message()
	{
	}

	/**
	 * Handle PHP and user messages.
	 *
	 * @access private
	 * @param  int     Message level.
	 * @param  string  Message content.
	 * @param  string  Full path to file that trigged the message.
	 * @param  int     File line that trigged the message.
	 * @param  array   Scope variables where message was trigged.
	 * @param  bool    Set if will dump $vars.
	 * @return void
	 */
	function _handler($level, $message, $file, $line, $vars = '', $dump_vars = FALSE)
	{
		// check error_reporting, reject messages with $level = 0
		if (! (ini_get('error_reporting') & $level) || $level == 0) {
			return;
		}

		$time = $memory = '';

		// check if show elapsed time was enabled
		if (   defined('MESSAGE_START_MICROTIME')
			&& defined('MESSAGE_SHOW_TIME') && MESSAGE_SHOW_TIME
		) {
			$time = '('. Message::getElapsedTime() .'s) ';
		}

		// check if show memory was enabled
		if (defined('MESSAGE_SHOW_MEMORY') && MESSAGE_SHOW_MEMORY)
		{
			if (function_exists('memory_get_usage')) {
				$memory = '('. number_format(memory_get_usage()) .'b) ';
			}
		}

		// get filename without full path
		if (! (defined('MESSAGE_FULL_FILEPATH') && MESSAGE_FULL_FILEPATH) )
		{
		//	$file = substr($file, strrpos($file, '/') + 1);
			$file = str_replace(OPENWORKS_DIR , '', $file);
			$file = str_replace(APPLICATION_DIR , '', $file);
		}

		// message labels
		$labels = array (
			MESSAGE_PHP_ERROR           => "PHP Error",
			MESSAGE_PHP_WARNING         => "PHP Warning",
			MESSAGE_PHP_PARSE           => "PHP Parser",
			MESSAGE_PHP_NOTICE          => "PHP Notice",
			MESSAGE_PHP_STRICT          => "PHP Strict",
			MESSAGE_PHP_USER_ERROR      => "PHP User Error",
			MESSAGE_PHP_USER_WARNING    => "PHP User Warning",
			MESSAGE_PHP_USER_NOTICE     => "PHP User Notice",

			MESSAGE_APP_ERROR   => "Application Error",
			MESSAGE_APP_WARNING => "Application Warning",
			MESSAGE_APP_ALERT   => "Application Alert",
			MESSAGE_APP_NOTICE  => "Application Notice",
			MESSAGE_APP_INFO    => "Application Info",
			MESSAGE_APP_DEBUG   => "Application Debug",
		);

		$output = $time . $memory;

		// check if label exists
		if (array_key_exists($level, $labels)) {
			$output .= $labels[$level];
		} else {
			$output .= "Message level #$level";
		}

		// format output message
		$output .= ": $file #$line: $message";

		// check if need dump variables
		if ($dump_vars) {
			$output .= ': '. Message::varDump($vars);
		} else {
			$output .= "\n";
		}

		// check if will log messages
		if (defined('MESSAGE_LOG_FLAG') && MESSAGE_LOG_FLAG)
		{
			// check message level to determine log name
			if ($level <= MESSAGE_PHP_ALL)
			{
				if (defined('MESSAGE_PHP_ERROR_LOG')) {
					$log_name = MESSAGE_PHP_ERROR_LOG;
				} else {
					$log_name = 'php.log';
				}
			}
			else
			{
				if (defined('MESSAGE_APP_ERROR_LOG')) {
					$log_name = MESSAGE_APP_ERROR_LOG;
				} else {
					$log_name = 'user.log';
				}
			}

			// check if log dir was set
			if (defined('MESSAGE_LOG_DIR')) {
				$log_dir = MESSAGE_LOG_DIR;
			}
			// if log dir was not set, use local dir
			else {
				$log_dir = './';
			}

			if (defined('MESSAGE_LOG_FORMAT')) {
				$log_format = MESSAGE_LOG_FORMAT;
			} else {
				$log_format = '[ Y-m-d H:i:s ]';
			}

			$log_output = date($log_format) .' '. $output;
			@error_log($log_output, 3, $log_dir . $log_name);
		}

		// check if will print messages
		if (defined('MESSAGE_DISPLAY_FLAG') && MESSAGE_DISPLAY_FLAG)
		{
			// check if will show messages in HTML format
			if (defined('MESSAGE_HTML_FORMAT') && MESSAGE_HTML_FORMAT) {
				$output = Message::_formatHtmlMessage($output, $level);
			}

			// check popup message reporting
			if (defined('MESSAGE_CONSOLE_DEBUGGING') && MESSAGE_CONSOLE_DEBUGGING &&
				defined('MESSAGE_CONSOLE_REPORTING') && ($level & MESSAGE_CONSOLE_REPORTING))
			{
				// check console options
				if (defined('MESSAGE_START_MICROTIME') && defined('MESSAGE_CONSOLE_DIR'))
				{

					if (MESSAGE_DEBUG) {
						html_dump('writing message in message console');
					}

					$console_file = Message::_getConsoleFilename();
					@error_log($output, 3, $console_file);
				}
			}
			// print output message to stdout
			else {
				print $output;
			}
		}

		// check message level and execute addicional actions
		switch ($level)
		{
			case MESSAGE_PHP_ERROR:
			case MESSAGE_PHP_PARSE:
			case MESSAGE_PHP_USER_ERROR:
			case MESSAGE_APP_ERROR:
				if (defined('MESSAGE_CONSOLE_DEBUGGING')
					&& MESSAGE_CONSOLE_DEBUGGING
				) {
				//	Message::warning(
				//		"Console debugging file: ". Message::_getConsoleFilename()
				//	);
					Message::clearConsoleBuffer();
				}

				// finish script
				exit;
		}
	}

	/**
	 * Clear message console buffer
	 *
	 * @access public
	 * @return void
	 */
	function clearConsoleBuffer()
	{
		// check if start microtime and temp dir was defined
		if (! defined('MESSAGE_START_MICROTIME') || ! defined('MESSAGE_CONSOLE_DIR')) {
			return FALSE;
		}

		$console_file = Message::_getConsoleFilename();
		@unlink($console_file);
	}

	/**
	 * Open message console window
	 *
	 * Get message buffer content, output to console window and
	 * clear console buffer.
	 *
	 * @access public
	 * @return void
	 */
	function openConsoleWindow()
	{
		// check console options
		if (! (defined('MESSAGE_START_MICROTIME')
			&& defined('MESSAGE_CONSOLE_DIR')
			&& defined('MESSAGE_CONSOLE_REPORTING')
			&& defined('MESSAGE_CONSOLE_DEBUGGING')
			&& MESSAGE_CONSOLE_DEBUGGING == TRUE)
		) {
			return;
		}

		// get popup buffer contents
		$console_file = Message::_getConsoleFilename();

		if (MESSAGE_DEBUG) {
			html_dump('reading message console file: '. $console_file);
		}

		// check if file was created
		if (! is_readable($console_file)) {
			return;
		}

		$console_contents = file_get_contents($console_file);
		$console_contents = explode("\n", $console_contents);

		if (MESSAGE_DEBUG) {
			html_dump('preparing message console');
		}

		$output  = "<script>\n";
		$output .= MESSAGE_CONSOLE_NAME ." = window.open";
		$output .= "(\"\", \"". MESSAGE_CONSOLE_NAME ."\", ";
		$output .= "\"width=750, height=530, left=50, top=50, ";
		$output .= "resizable, scrollbars=yes\")\n";
		$output .= MESSAGE_CONSOLE_NAME .".document.write";
		$output .= "(\"<html><head><title>Message Console</title></head><body>\\n\")\n";

		foreach ($console_contents as $content)
		{
			$content = str_replace('"', "'", $content);
			$output .= MESSAGE_CONSOLE_NAME .".document.write(\"$content\\n\")\n";
		}

		$output .= MESSAGE_CONSOLE_NAME .".document.write(\"</body></html>\")\n";
		$output .= MESSAGE_CONSOLE_NAME .".document.close()\n";
		$output .= "</script>\n";

		print $output;

		// clear popup buffer
		Message::clearConsoleBuffer();
	}

	/**
	 * Closes defined console window
	 *
	 * @access public
	 * @return void
	 */
	function closeConsoleWindow($console_name)
	{
		$output  = "<script>\n";
		$output .= $console_name ." = window.open";
		$output .= "(\"\", \"$console_name\", \"\")\n";
		$output .= $console_name .".document.write(\"<script>\")\n";
		$output .= $console_name .".document.write(\"self.close()\")\n";
		$output .= $console_name .".document.write(\"<\")\n";
		$output .= $console_name .".document.write(\"/\")\n";
		$output .= $console_name .".document.write(\"script\")\n";
		$output .= $console_name .".document.write(\">\")\n";
		$output .= $console_name .".document.close()\n";
		$output .= "</script>\n";

		print $output;
	}

	/**
	 * Wrapper for user error messages
	 *
	 * @access public
	 * @return void
	 */
	function error($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_ERROR,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	 * Wrapper for user warning messages
	 *
	 * @access public
	 * @return void
	 */
	function warning($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_WARNING,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	 * Wrapper for user alert messages
	 *
	 * @access public
	 * @return void
	 */
	function alert($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_ALERT,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	 * Wrapper for user notice messages
	 *
	 * @access public
	 * @return void
	 */
	function notice($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_NOTICE,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	 * Wrapper for user information messages
	 *
	 * @access public
	 * @return void
	 */
	function info($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_INFO,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	 * Wrapper for user debugging messages
	 *
	 * @access public
	 * @return void
	 */
	function debug($message, $vars = '', $traceLevel = 0)
	{
		$debug = debug_backtrace();
		Message::_handler(
			MESSAGE_APP_DEBUG,
			$message,
			$debug[$traceLevel]['file'],
			$debug[$traceLevel]['line'],
			$vars,
			(!empty($vars))
		);
	}

	/**
	* Dump array values
	*
	* @access public
	* @param mixed $variable variable to be dumped
	* @param string $dump_function function used to dump variable
	* @return string dumped variable
	*/
	function varDump($variable, $dump_function = 'print_r')
	{
		// store variable content in buffer
		ob_start();
		$dump_function($variable);
		$content = ob_get_contents();
		ob_end_clean();

		// return variable content
		return $content;
	}

	/**
	 * Return elapsed time from a given microtime
	 *
	 * @access public
	 * @param float $start microtime used as start time
	 * @param int $precision time precision
	 * @return float elapsed time from $start
	 */
	function getElapsedTime($start = '', $precision = '')
	{
		// get current time
		$current = microtime();

		if (empty($start))
		{
			if (defined('MESSAGE_START_MICROTIME')) {
				$start = MESSAGE_START_MICROTIME;
			} else {
				return 0;
			}
		}

		if (empty($precision))
		{
			if (defined('MESSAGE_TIME_PRECISION')) {
				$precision = MESSAGE_TIME_PRECISION;
			} else {
				$precision = 3;
			}
		}

		// parse microtime string
		$now   = Message::_parseMicrotime($current, $precision);
		$start = Message::_parseMicrotime($start, $precision);

		// calculate and round times
		$time = round($now - $start, $precision);

		// format string using $precision value
		$format = "%.". $precision ."f";
		$time = sprintf($format, $time);

		return $time;
	}

	/**
	 * Format message with HTML tags
	 *
	 * @access private
	 * @param string $message message to format
	 * @param int $level message level
	 * @return string
	 */
	 function _formatHtmlMessage($message, $level)
	 {
		// check if font family was defined
		if (defined('MESSAGE_HTML_FONT_FAMILY')) {
			$font_family = MESSAGE_HTML_FONT_FAMILY;
		} else {
			$font_family = 'Verdana, Helvetica';
		}

		// check if font size was defined
		if (defined('MESSAGE_HTML_FONT_SIZE')) {
			$font_size = MESSAGE_HTML_FONT_SIZE;
		} else {
			$font_size = '10px';
		}

		// HTML message colors
		$colors = array(
			 MESSAGE_PHP_ERROR        => '#800000'
			,MESSAGE_PHP_WARNING      => '#D00000'
			,MESSAGE_PHP_USER_ERROR   => '#800000'
			,MESSAGE_PHP_USER_WARNING => '#D00000'

			,MESSAGE_APP_ERROR        => '#800000'
			,MESSAGE_APP_WARNING      => '#D00000'
			,MESSAGE_APP_ALERT        => '#007000'
			,MESSAGE_APP_NOTICE       => '#00D000'
			,MESSAGE_APP_INFO         => '#000070'
			,MESSAGE_APP_DEBUG        => '#0000D0'
		);

		// HTML message style
		$style  = "font-family: $font_family; ";
		$style .= "font-size: $font_size; ";

		if (isset($colors[$level])) {
			$style .= "color: $colors[$level];";
		}

		$message = nl2br($message);
		$message = str_replace('<br />', '<br/>', $message);

		$formated  = '<font style="'. $style .'">';
		$formated .= str_replace(chr(32),'&nbsp;', $message);
		$formated .= "</font>\n";

		return $formated;
	}

	/**
	 * Returns full path to console buffer file
	 *
	 * @access private
	 * @return string buffer console filename
	 */
	function _getConsoleFilename()
	{
		$console_file = MESSAGE_CONSOLE_DIR ."console_". md5(MESSAGE_START_MICROTIME);
		return $console_file;
	}

	/**
	 * Parse string returned by microtime()
	 *
	 * @access private
	 * @param string $microtime string returned by microtime()
	 * @param int $precision decimail precision of time
	 * @return array hash with integer and decimal portion of microtime
	 */
	function _parseMicrotime($microtime, $precision)
	{
		// separate values
		$integer = substr($microtime, 10, strlen($microtime));
		$decimal = substr($microtime, 0, 10);

		// set correct variable types
		settype($integer, 'integer');

		if (strcmp(phpversion(), '4.2.0') >= 0) {
			settype($decimal, 'float');
		}
		else {
			settype($decimal, 'double');
		}

		// sum integer and decimal values
		$time = $integer + $decimal;

		return $time;
	}

}

/**
 * Wrapper to Message::_handler for compability with versions < 4.3.0
 *
 * @see Message::_handler
 */
function message_handler($level, $message, $file, $line, $vars = '', $dump_vars = FALSE)
{
	Message::_handler($level, $message, $file, $line, $vars, $dump_vars);
}

if (! function_exists('debug_backtrace')):
/**
 * Backward compability with previous versions
 */
function debug_backtrace()
{
	$debug = array(array(
		 'file' => 'unknow'
		,'line' => 0
	));
}
endif;

?>
