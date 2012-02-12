<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Dao.class.php - Implements DAO design pattern for data abstration.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

define('DAO_QUERY_ERROR', -1);
define('DAO_KEY_ERROR',   -2);

/**
 * class Dao
 *
 * @package openworks
 */
class Dao
{

    /**
     * Returns a reference to the global DAO object, only
     * creating it if it doesn't already exist.
     *
     * This method must be invoked as: $obj = &Dao::singleton($dsn)
	 * Also must have a pre-defined constant DAO_$dsn with the DSN string.
     *
	 * @param  string DAO datasource name.
	 * @param  bool   Define if will return a Dao instance or a DaoFactory instance.
     * @return object DAO instance.
     */
    function &singleton ($dsn_name, $returnFactoryInstance = FALSE)
    {
		$dsn_name = 'DAO_'. strtoupper($dsn_name);
		$dsn = constant($dsn_name);

		static $instance;

        if (! isset($instance[$dsn_name])) {
            $instance[$dsn_name] = &Dao::factory($dsn);
        }

		if ($returnFactoryInstance) {
        	return $instance[$dsn_name]->getFactoryInstance();
		} else {
        	return $instance[$dsn_name];
		}
    }

	function &factory ($dsn)
	{
		$array = explode('://', $dsn);
		$dao_type = strtolower($array[0]);
		$factories = explode(' ', DAO_FACTORY);

		foreach ($factories as $factory)
		{
			$constant = 'DAO_FACTORY_'. strtoupper($factory);
			$types = explode(' ', constant($constant));

			if (array_search($dao_type, $types) !== FALSE)
			{
				$dao_factory = $factory;
				continue;
			}
		}

		if (! isset($dao_factory))
		{
			trigger_error('No factories defined for this datasource type', E_USER_ERROR);
			return FALSE;
		}

		$factory_class = ucfirst(strtolower($dao_factory)) .'DaoFactory';
		require_once OPENWORKS_CORE_DIR ."Dao/$factory_class.class.php";

		$dao_factory = &new $factory_class($dsn);
		return $dao_factory->getInstance();
	}

}

?>