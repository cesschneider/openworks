<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * sql.lib.php - SQL functions.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage core
 * @version 1.0
 */

function sql_strip_slashes($values)
{
	if (is_array($values))
	{
		foreach ($values as $key => $value) {
			$values[$key] = str_replace("\'", "'", $value);
		}
	}

	return $values;
}

/**
 * Return SQL string for defined instruction, table and values.
 *
 * @param  string SQL instruction
 * @param  string Table name
 * @param  array  Associative array with values
 * @return string 
 */
function sql_get_string ($command, $table, $values)
{
	if (! is_array($values))
	{
		trigger_error('Third parameter must be an associative array', E_USER_WARNING);
		return FALSE;
	}
		
	$sql_fields = $sql_values = '';
	foreach ($values as $field => $value)
	{
	//	print $value;

		// add slashes
	//	if (ereg("'", $value) && !ereg("\'", $value)) {
			$value = stripslashes($value);
			$value = str_replace("'","\'", $value);
	//	}
		
		if (! empty($sql_fields)) {
			$sql_fields .= ', ';
		}
		
		if (! empty($sql_values)) {
			$sql_values .= ', ';
		}
		
		if ($command == 'insert' || $command == 'replace')
		{
			$sql_fields .= "$field";
			$sql_values .= "'$value'";
		} 
		else if ($command == 'update')
		{
			$sql_fields .= "$field = '$value'";
		}
	}
	
	if ($command == 'insert' || $command == 'replace') {
		$sql = strtoupper($command) ." INTO $table ($sql_fields) VALUES ($sql_values) ";
	}
	else if ($command == 'update') {
		$sql = "UPDATE $table SET $sql_fields ";
	}
	
	return $sql;
}

function sql_insert_string ($table, $values)
{
	if (! is_array($values))
	{
		trigger_error('Second parameter must be an associative array', E_USER_WARNING);
		return FALSE;
	}

	return sql_get_string('insert', $table, $values);
}

function sql_replace_string ($table, $values)
{
	if (! is_array($values))
	{
		trigger_error('Second parameter must be an associative array', E_USER_WARNING);
		return FALSE;
	}

	return sql_get_string('replace', $table, $values);
}

function sql_update_string ($table, $values)
{
	if (! is_array($values))
	{
		trigger_error('Second parameter must be an associative array', E_USER_WARNING);
		return FALSE;
	}

	return sql_get_string('update', $table, $values);
}
