<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Sql.class.php - SQL methods.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage core
 * @version 1.0
 */

define ('SQL_COMMAND_SELECT',  1);
define ('SQL_COMMAND_INSERT',  2);
define ('SQL_COMMAND_REPLACE', 3);
define ('SQL_COMMAND_UPDATE',  4);
define ('SQL_COMMAND_DELETE',  5);

/**
 * class Sql
 */
class Sql
{
	/**
	 * Return SQL string for defined instruction, table and values.
	 *
	 * @param  string SQL instruction
	 * @param  string Table name
	 * @param  array  Associative array with values
	 * @return string 
	 */
	function getString ($command, $table, $values)
	{
		if (! is_array($values))
		{
			trigger_error('Third parameter must be an associative array', E_USER_WARNING);
			return FALSE;
		}
			
		$sql = $sql_fields = $sql_values = '';
		
		foreach ($values as $field => $value)
		{
			if (! empty($sql_fields)) {
				$sql_fields .= ', ';
			}
			
			if (! empty($sql_values)) {
				$sql_values .= ', ';
			}
						
			if ($command == 'insert' || $command == 'replace')
			{
				$sql_fields .= Sql::quote($field, '"');
				$sql_values .= Sql::quote($value);
			} 
			else if ($command == 'update')
			{
				$sql_fields .= Sql::quote($field, '"') . ' = ' . Sql::quote($value) .' ';
			}
			else if ($command == 'delete'){
			}
		}
		
		if ($command == 'insert' || $command == 'replace') {
			$sql = strtoupper($command) ." INTO \"$table\" ($sql_fields) VALUES ($sql_values) ";
		}
		else if ($command == 'update') {
			$sql = "UPDATE \"$table\" SET $sql_fields ";
		}
		else if ($command == 'delete') {
			$sql = "DELETE FROM \"$table\" ";
		}
		
		return $sql;
	}
	
	function insert ($table, $values)
	{
		if (! is_array($values))
		{
			trigger_error('Second parameter must be an associative array', E_USER_WARNING);
			return FALSE;
		}
	
		return Sql::getString('insert', $table, $values);
	}
	
	function replace ($table, $values)
	{
		if (! is_array($values))
		{
			trigger_error('Second parameter must be an associative array', E_USER_WARNING);
			return FALSE;
		}
	
		return Sql::getString('replace', $table, $values);
	}
	
	function update ($table, $values, $criteria)
	{
		if (! is_array($values))
		{
			trigger_error('Second parameter must be an associative array', E_USER_WARNING);
			return FALSE;
		}
		if (! is_array($criteria))
		{
			trigger_error('Third parameter must be an array', E_USER_WARNING);
			return FALSE;
		}
	
		$sql = Sql::getString('update', $table, $values);
		
		$condition = '';
		foreach ($criteria as $field) 
		{
			$condition .= (empty($condition)) ? 'WHERE ' : 'AND ';
			$condition .= Sql::quote($field, '"') .' = '. Sql::quote($values[$field]) .' ';
		}
		
		$sql .= $condition;
		
		return $sql;
	}

	function delete ($table, $values, $criteria)
	{
		if (! is_array($values))
		{
			trigger_error('Second parameter must be an associative array', E_USER_WARNING);
			return FALSE;
		}
		
		if (! is_array($criteria))
		{
			trigger_error('Third parameter must be an array', E_USER_WARNING);
			return FALSE;
		}
	
		$sql = Sql::getString('delete', $table, $values);
		
		$condition = '';
		foreach ($criteria as $field) 
		{
			$condition .= (empty($condition)) ? 'WHERE ' : 'AND ';
			$condition .= Sql::quote($field, '"') .' = '. Sql::quote($values[$field]) .' ';
		}
		
		$sql .= $condition;
		return $sql;
	}

	
	function quote ($string, $character = "'")
	{
		$string =  $character . str_replace($character, '\\'. $character, $string) . $character;
		return $string;	
	}

}

?>