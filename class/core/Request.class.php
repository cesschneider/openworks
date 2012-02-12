<?php

//--------------------------------------------------------------------------
// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.
//--------------------------------------------------------------------------

/**
 * Request.class.php - Implements the Command design pattern.
 *
 * This class parses user request and parameters.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

/**
 * class Request
 *
 * @package openworks
 */
class Request
{
	var $action_request;

	var $action_file;

	var $action_trace;

	var $action_map;

	var $user_parameters;

	var $forward_request;

    /**
     * Returns a reference to the global Request object, only
     * creating it if it doesn't already exist.
     *
     * This method must be invoked as: $obj = &Request::singleton()
     *
     * @return object Request instance.
     */
    function &singleton ()
    {
		static $instance;

        if (! isset($instance)) {
            $instance = new Request;
        }

        return $instance;
    }

	function parseUserRequest()
	{
		if (! empty($_GET))
		{
			$keys = array_keys($_GET);
			$this->action_request = $keys[0];

			if (count($keys) > 1)
			{
				$parameters = explode(',', $keys[1]);
				foreach ($parameters as $key => $value)
				{
					$key++;

					if (ereg(':', $value))
					{
						$param = explode(':', $value);
						$key   = $param[0];
						$value = $param[1];
					}

					$this->user_parameters[$key] = $value;
				}
			}
		}
		else {
			$this->action_request = REQUEST_DEFAULT_ACTION;
		}

		$this->action_request = ereg_replace("(/){1,}", '.', $this->action_request);
		$this->action_request = ereg_replace("(_){1,}", '.', $this->action_request);
		$this->action_request = ereg_replace("^\.|\.$", '', $this->action_request);
	}

	function getAttributes ()
	{
		$attributes = array(
			 'action_request'  => $this->action_request
			,'action_file'     => $this->action_file
			,'action_trace'    => $this->action_trace
			,'action_map'      => $this->action_map
			,'user_parameters' => $this->user_parameters
		);

		return $attributes;
	}

	function getUserParameters ()
	{
		$request = &Request::singleton();
		return $request->user_parameters;
	}

	function checkActionRequest ()
	{
		Message::info('Checking action request: '. $this->action_request);

		$this->action_map = explode('.', $this->action_request);

		// try to found action file using action request
		$this->action_file = $this->action_request;

		if (file_exists($this->getActionFile()))
		{
			Message::debug('Action file founded using action request');
			return TRUE;
		}
		else
		{
			Message::debug('Searching action file using action map');

			$this->action_file = '';

			// try to found action file using action map
			foreach ($this->action_map as $node)
			{
				$this->action_file .= $node;

			//	Message::debug(APPLICATION_ACTION_DIR);

				if (is_dir(APPLICATION_ACTION_DIR . $this->action_file))
				{
					Message::debug('Found directory: '. $node);
					$this->action_file .= '/';
				}
				else if ( is_file($this->getActionFile()) )
				{
					Message::debug('Found file: '. $node);
					$this->action_file .= '.';
				}
				else
				{
					Message::debug('Invalid path: '. $this->action_file);
					return FALSE;
				}
			}

			// remove last character
			$this->action_file = substr($this->action_file, 0, -1);

			if ( file_exists($this->getActionFile()) )
			{
				Message::debug('Action file founded: '. $this->action_file);
				return TRUE;
			}
		}

		return FALSE;
	}

	function getActionFile ()
	{
		return APPLICATION_ACTION_DIR . $this->action_file .'.action.php';
	}

	function getActionClass ()
	{
		$array = explode('.', $this->action_request);

		foreach ($array as $value) {
			@$class_name .= ucfirst($value);
		}

		return $class_name .'Action';
	}

	function forwardRequest ($action_request)
	{
		Message::info('Forwarding request to: '. $action_request);

		$this->action_request = $action_request;
		$this->forward_request = TRUE;
	}

	function updateActionTrace()
	{
		$this->action_trace[] = $this->action_request;
	}
}

?>
