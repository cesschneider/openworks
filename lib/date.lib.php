<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * date.lib.php - Functions to manipulate date.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage core
 * @version 1.0
 */

define('DATE_FORMAT_YMD', 1);
define('DATE_FORMAT_DMY', 2);

/**
 * Returns the next util day from a defined date and days ahead.
 *
 * @param  mixed UNIX timestamp or
 *               database timestamp (Y-m-d H:M:S) or
 *               simple date (Y-m-d)
 * @param  int   number of days ahead
 * @param  bool  flag to return a array with each day
 * @return mixed
 */
function date_get_util_day ($timestamp, $days, $array_flag = FALSE)
{
	// check if parameter is a UNIX timestamp
	if (is_int($timestamp))
	{
		$unix_flag = TRUE;
		$unix_timestamp = $timestamp;
	}
	else
	{
		$unix_flag = FALSE;
		$unix_timestamp = date_get_unix_timestamp($timestamp);
	}

	$week_day = date('w', $unix_timestamp);

	// if atual day is saturday, add 1 day to fix bug on loop
	if ($week_day == 6) {
		$unix_timestamp += 86400;
	}

	// days loop
	for ($day = 1; $day <= $days; $day++)
	{
		// add 1 day
		$unix_timestamp += 86400;
		$week_day = date('w', $unix_timestamp);

		// if next day is saturday, add 2 days
		if ($week_day == 6) {
			$unix_timestamp += (86400 * 2);

		// if next day is sunday, add 1 day
		} else if ($week_day == 0) {
			$unix_timestamp += 86400;
		}

		// check array flag
		if ($array_flag)
		{
			if ($unix_flag) {
				$days_array[$day] = $unix_timestamp;
			} else {
				$days_array[$day] =
					substr(date('Y-m-d H:i:s', $unix_timestamp), 0, strlen($timestamp));
			}
		}
	}

	// return an array with days
	if ($array_flag) {
		return $days_array;
	
	// return an UNIX timestamp
	} else if ($unix_flag) {
		return $unix_timestamp;

	// return a formated date
	} else {
		return substr(date('Y-m-d H:i:s', $unix_timestamp), 0, strlen($timestamp));

	}
}

/**
 * Returns UNIX timestamp from a database timestamp.
 *
 * @param  string database timestamp (Y-m-d H:M:S) or simple date (Y-m-d)
 * @return int    UNIX timestamp
 */
function date_get_unix_timestamp ($timestamp)
{
	// splip timestamp
	$mktime['year']   = substr($timestamp, 0, 4);
	$mktime['month']  = substr($timestamp, 5, 2);
	$mktime['day']    = substr($timestamp, 8, 2);

	// check if timestamp has time
	if (strlen($timestamp) > 10)
	{
		$mktime['hour']   = substr($timestamp, 11, 2);
		$mktime['minute'] = substr($timestamp, 14, 2);
		$mktime['second'] = substr($timestamp, 17, 2);
	}
	else {
		$mktime['hour'] = $mktime['minute'] = $mktime['second'] = 0;
	}

	// convert to UNIX timestamp
	$unix_timestamp = mktime(
		$mktime['hour'], $mktime['minute'], $mktime['second'],
		$mktime['month'], $mktime['day'], $mktime['year'], 0
	);

	return $unix_timestamp;
}

function date_change_format ($date, $format = NULL)
{
	if (strlen($date) < 10)
	{
		trigger_error('Invalid date length', E_USER_NOTICE);
		return $date;
	}

	if (is_null($format))
	{
		if (ereg('-', $date)) {
			$format = DATE_FORMAT_YMD;
		}
		else if (ereg('/', $date)) {
			$format = DATE_FORMAT_DMY;
		}
	}

	switch ($format)
	{
		case DATE_FORMAT_YMD:
			$separator1 = '-';
			$separator2 = '/';
			break;

		case DATE_FORMAT_DMY:
			$separator1 = '/';
			$separator2 = '-';
			break;

		default:
			trigger_error('Invalid date format', E_USER_NOTICE);
			return $date;
	}

	$values = explode($separator1, $date);
	$date = $values[2] . $separator2 . $values[1] . $separator2 . $values[0];

	return $date;
}

function date_get_last_day ($month, $year)
{
	for ($day = 31; $day >= 28; $day--)
	{
		if (checkdate($month, $day, $year)) {
			return $day;
		}
	}

	return FALSE;
}

function date_get_age ($birthday, $date)
{
	if ($birthday == '0000-00-00' || empty($birthday) || !ereg('-', $birthday)) {
		return FALSE;
	}
	if ($date == '0000-00-00' || empty($date) || !ereg('-', $date)) {
		return FALSE;
	}

	$birthday = explode('-', $birthday);
	$date = explode('-', $date);

	if (count($birthday) != 3 || count($date) != 3) {
		return FALSE;
	}

	$age = $date[0] - $birthday[0];

	if ($age > 0 &&
		$date[1] < $birthday[1] ||
		($date[1] == $birthday[1] && $date[2] < $birthday[2])
	) {
		$age--;
	}

	return $age;
}

function date_get_birthday ($age, $date)
{
	$date = explode('-', $date);
	$birthday = ((int)$date[0] - (int)$age) .'-'. $date[1] .'-'. $date[2];

	return $birthday;
}

?>