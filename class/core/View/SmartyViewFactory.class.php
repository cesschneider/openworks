<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * SmartyViewFactory.class.php - Smarty factory class.
 *
 * This classe encapsulates the common methods of Smarty and
 * provide integration with View methods.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

require_once OPENWORKS_CORE_DIR .'ViewFactory.class.php';

/**
 * Factory class that provides integration with Smarty class.
 */
class SmartyViewFactory extends ViewFactory
{
    /**
     * Returns a reference to the global SmartyViewFactory object, only
     * creating it if it doesn't already exist.
     *
	 * IMPORTANT: this method must be called dinamically.
	 *
     * @return object SmartyViewFactory instance.
     */
	function SmartyViewFactory ()
	{
		require_once SMARTY_DIR .'Smarty.class.php';

		// create a new Smarty instance
		$this->factory_instance = new Smarty;

		// set some parameters
		$this->factory_instance->template_dir = SMARTY_TEMPLATE_DIR;
		$this->factory_instance->compile_dir  = SMARTY_COMPILE_DIR;
		$this->factory_instance->debugging    = SMARTY_DEBUGGING;

		if (defined('SMARTY_FORCE_COMPILE')) {
			$this->factory_instance->force_compile  = SMARTY_FORCE_COMPILE;
		}

		$dirs = explode(' ', SMARTY_PLUGINS_DIR);
		foreach ($dirs as $dir) {
			$this->factory_instance->plugins_dir[] = $dir;
		}
    }

	function assign ($variable, $value = NULL)
	{
		$this->factory_instance->assign($variable, $value);
	}

	function append ($variable, $value = NULL, $merge = FALSE)
	{
		$this->factory_instance->append($variable, $value, $merge);
	}

	function fetch ($template_file)
	{
		return $this->factory_instance->fetch($template_file);
	}

	function display ()
	{
		$this->factory_instance->display($this->template_file);
	}
}

?>