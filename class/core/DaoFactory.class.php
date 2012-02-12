<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * DaoFactory.class.php - Base class for DaoFactory classes.
 *
 * This classe provides integration between Dao common methods and
 * external (factory) classes.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

/**
 * class DaoFactory
 *
 * @package openworks
 */
class DaoFactory
{
	/** DSN string used in this factory */
	var $dsn_string;

	/** Factory object instance */
	var $factory_instance;

	/** Dao schema file */
	var $schema_file;

	/**
	 * Constructor
	 */
	function DaoFactory ($dsn_string)
	{
		$this->dsn_string = $dsn_string;
	}

	/**
	 * Returns the factory object instance.
	 *
	 * @return object
	 */
	function &getFactoryInstance()
	{
		return $this->factory_instance;
	}

	function setSchemaFile ($schema_file)
	{
		$this->schema_file = $schema_file;
	}

	//
	// Methods from this point are implemented by child classes.
	//

	function startTransaction ()
	{

	}

	function completeTransaction ()
	{

	}

	function select ($entities, $atributes, $condition)
	{

	}

	function insert ($entity, $values)
	{

	}

	function replace ($entity, $values)
	{

	}

	function update ($entity, $values, $condition)
	{

	}

	function delete($entity, $condition)
	{

	}

	function validate ($values)
	{

	}
}

?>