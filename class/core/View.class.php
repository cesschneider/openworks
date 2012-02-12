<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * View.class.php - Provide methods to manipulate presentation logic and values.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

/**
 * class View
 *
 * @package openworks
 */
class View
{
	
    /**
     * Returns a reference to the global View object, only
     * creating it if it doesn't already exist.
     *
     * This method must be invoked as: $obj = &View::singleton($dsn)
     *
	 * @param  string View type related to a factory.
     * @return object ViewFactory instance.
     */
    function &singleton ($view_type)
    {
		static $instance;

        if (! isset($instance[$view_type]))
		{
			$factory_class = View::getFactoryClass($view_type);

			require_once OPENWORKS_CORE_DIR ."View/$factory_class.class.php";
            $instance[$view_type] = &new $factory_class;
        }

        return $instance[$view_type];
    }

	function getFactoryClass ($view_type)
	{
		$factories = explode(' ', VIEW_FACTORY);

		foreach ($factories as $factory)
		{
			$constant = 'VIEW_FACTORY_'. strtoupper($factory);
			$types = explode(' ', constant($constant));

			if (array_search($view_type, $types) !== FALSE)
			{
				$view_factory = $factory;
				continue;
			}
		}

		if (! isset($view_factory))
		{
			trigger_error('No factories defined for this view type', E_USER_ERROR);
			return FALSE;
		}

		$factory_class = ucfirst(strtolower($view_factory)) .'ViewFactory';
		return $factory_class;
	}

}

?>