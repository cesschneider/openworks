<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Model.class.php - Base class used by model (business rules) classes.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

define ('MODEL_VALIDATE_ERROR', 'mve');

/**
 * class Model
 *
 * @package openworks
 */
class Model
{
	/** Session values of model class */
	var $session;

	/** Error message defined by a method */
	var $error_message;

	/** Error code defined by a method */
	var $error_code;

	/** Validate messages defined by a method */
	var $validate_message;

	/** Number of rows of a query */
	var $rows;

	function Model ($model)
	{
		$this->error_message = '';
		$this->error_code    = 0;
		
		$this->validate_message = array();
		
		$this->rows = 0;

		$model = strtolower($model);
		$this->session =& $_SESSION[$model];
	}

    /**
     * Returns a reference to the global model class object, only
     * creating it if it doesn't already exist.
     *
     * This method must be invoked as: $obj = &Model::singleton('class')
     *
	 * @param  string Model class name.
     * @return object The especified class instance.
     */
    function &singleton ($model)
    {
		require_once APPLICATION_MODEL_DIR . "$model.model.php";
		$class_name = $model .'Model';

		static $instance;

        if (! isset($instance[$class_name])) {
            $instance[$class_name] = &new $class_name ($model);
        }

        return $instance[$class_name];
    }

	function getErrorCode ()
	{
		return $this->error_code;
	}

	function getErrorMessage ()
	{
		return $this->error_message;
	}

	function setErrorCode ($error_code)
	{
		$this->error_code = $error_code;
	}

	function setErrorMessage ($error_message)
	{
		$this->error_message = $error_message;
	}

	function setValidateMessage ($field = NULL, $value = NULL)
	{
		if (is_null($field) && is_null($value)) {
			$this->validate_message = array();
		} else {
			$this->validate_message[$field] = $value;
		}
	}
	
	function getValidateMessage ($field = NULL)
	{
		if (is_null($field)) {
			return $this->validate_message;
		} else {
			return $this->validate_message[$field];
		}
	}
	
	function setSession ($field, $value)
	{
		$this->session[$field] = $value;
	}
	
	function getSession ($field = NULL)
	{
		if (is_null($field)) {
			return $this->session;
		} else {
			return $this->session[$field];
		}
	}
	
	function setRows ($rows)
	{
		$this->rows = $rows;
	}
	
	function getRows ()
	{
		return $this->rows;
	}
}

?>