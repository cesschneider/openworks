<?php

define('FILTER_PRE_ACTION_TYPE',  'pre');
define('FILTER_POST_ACTION_TYPE', 'post');

/**
 * Implements the Intercept Filter design pattern.
 */
class FilterChain
{

	var $filters;

    /**
     * Returns a reference to the global FilterChain object, only
     * creating it if it doesn't already exist.
     *
     * This method must be invoked as: $obj = &FilterChain::singleton()
     *
     * @return object FilterChain instance.
     */
    function &singleton ()
    {
		static $instance;

        if (! isset($instance)) {
            $instance = new FilterChain;
        }

        return $instance;
    }

	function executeFilters ($action_request, $filter_type)
	{
		$filter_list = $this->getFilterList ($action_request, $filter_type);

		if (! is_array($filter_list)) {
			return;
		}

		foreach ($filter_list as $filter_name) {
			$this->executeFilter ($filter_name);
		}
	}

	function executeFilter ($filter_name)
	{
		$class_name = $this->getFilterClass ($filter_name);
		require_once APPLICATION_FILTER_DIR . $filter_name . '.filter.php';

		$filter = new $class_name;
		$filter->execute();
	}

	function getFilterList ($action_request, $filter_type)
	{
		if (isset($this->filters[$filter_type][$action_request])) {
			return $this->filters[$filter_type][$action_request];
		}

		if ($filter_type == FILTER_PRE_ACTION_TYPE) {
			$constant = 'FILTER_PRE_ACTION_MATCH';
		} else if ($filter_type == FILTER_POST_ACTION_TYPE) {
			$constant = 'FILTER_POST_ACTION_MATCH';
		} else {
			trigger_error("Invalid filter type: $filter_type", E_USER_WARNING);
			return FALSE;
		}

		$filters = explode(' ', constant($constant));

		if (! is_array($filters)) {
			return array();
		}

		foreach ($filters as $filter)
		{
			$array = explode(':', $filter);

			if (isset($array[1]) && ereg($array[1], $action_request)) {
				$this->filters[$filter_type][$action_request][] = $array[0];
			}
		}

		if (isset($this->filters[$filter_type][$action_request])) {
			return $this->filters[$filter_type][$action_request];
		} else {
			return array();
		}
	}

	function getFilterClass ($filter_name)
	{
		$array = explode('.', $filter_name);

		foreach ($array as $value) {
			@$class_name .= ucfirst($value);
		}

		return $class_name .'Filter';
	}
}

?>