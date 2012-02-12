<?php

require_once OPENWORKS_LIB_DIR .'date.lib.php';

function smarty_get_date ($select_date, $unix_timestamp = FALSE)
{
	$date  = sprintf('%04d', $select_date['Year']) .'-';
	$date .= sprintf('%02d', $select_date['Month']) .'-';
	$date .= sprintf('%02d', $select_date['Day']);

	if ($unix_timestamp) {
		$date = date_get_unix_timestamp($date);
	}

	return $date;
}

function smarty_get_time ($select_time, $unix_timestamp = FALSE)
{

}

?>