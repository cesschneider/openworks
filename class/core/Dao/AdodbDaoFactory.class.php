<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * AdodbDaoFactory.class.php - ADOdb factory class.
 *
 * This classe encapsulates the common methods of ADOdb and
 * provide integration with DAO methods.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

ini_set('include_path', OPENWORKS_EXT_DIR .'pear'. PATH_SEPARATOR . ini_get('include_path'));

require_once OPENWORKS_CORE_DIR .'DaoFactory.class.php';
require_once 'PEAR.php';

//PEAR::setErrorHandling('PEAR_ERROR_TRIGGER', NULL, E_USER_WARNING);
PEAR::setErrorHandling(PEAR_ERROR_CALLBACK, 'PEAR_call_back');

function PEAR_call_back($error)
{
	trigger_error($error->getMessage() .': '. $error->getDebugInfo(), E_USER_WARNING);
}

/**
 * class AdodbDaoFactory
 *
 * @package openworks
 */
class AdodbDaoFactory extends DaoFactory
{

    /**
     * Returns a reference to the global AdodbDaoFactory object, only
     * creating it if it doesn't already exist.
     *
	 * IMPORTANT: this method must be called dinamically.
	 *
     * @return objectAdodb DaoFactory instance.
     */
	function &getInstance ()
	{
		require_once ADODB_DIR .'adodb.inc.php';
		require_once ADODB_DIR .'adodb-errorpear.inc.php';

		// create a new ADOdb instance
		$this->factory_instance = &ADONewConnection($this->dsn_string);

		if (FALSE !== $this->factory_instance)
		{
			// set associative fetch mode
			$this->factory_instance->setFetchMode(ADODB_FETCH_ASSOC);

			// returns AdodbDaoFactory instance
			return $this;
		}
		else
		{
			trigger_error('DAO connection error on DSN '. $this->dsn_string);
			return FALSE;
		}
    }

	function getNextId ($seq_name)
	{

	}
}

?>