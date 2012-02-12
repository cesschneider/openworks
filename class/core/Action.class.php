<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Action.class.php - Implements the Page Controller design pattern.
 *
 * This class is used as base class for all application action classes.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

/**
 * class Action
 *
 * @package openworks
 */
class Action
{
	
	function initialize ()
	{

	}

	function validate ()
	{

	}

	function execute (&$request, &$view)
	{
		$class = get_class($this);
		trigger_error("You must implement the execute() method of $class class", E_USER_WARNING);
	}

	function display ()
	{

	}
	
}

?>