<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * ViewFactory.class.php - Base class used to integrate View methods with external classes.
 *
 * This class provides integration between View common methods and external (factory) classes.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

/**
 * class ViewFactory
 *
 * @package openworks
 */
class ViewFactory
{
	/** Factory object instance */
	var $factory_instance;

	/** Template file used to render output */
	var $template_file;


	/**
	 * Returns the factory object instance.
	 *
	 * @return object
	 */
	function &getFactoryInstance ()
	{
		return $this->factory_instance;
	}

	function setTemplateFile ($template_file)
	{
		$this->template_file = $template_file;
	}

	function getTemplateFile ()
	{
		return $this->template_file;
	}

	function assign ($variable, $value = NULL)
	{
		trigger_error('This method must be implement by child class', E_USER_WARNING);
	}

	function append ($variable, $value = NULL, $merge = FALSE)
	{
		trigger_error('This method must be implement by child class', E_USER_WARNING);
	}

	function fetch ($template_file)
	{
		trigger_error('This method must be implement by child class', E_USER_WARNING);
	}

	function display ()
	{
		trigger_error('This method must be implement by child class', E_USER_WARNING);
	}

}

?>