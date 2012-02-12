<?php

// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.

/**
 * Pager.class.php - Methods used to create paged results.
 *
 * @author Cesar Schneider <cesschneider at users sf dot net>
 * @package openworks
 * @subpackage util
 * @version 1.0
 */

/**
 * class Pager
 */
class Pager
{
	/**
	 * Return an array with all values used in paged results.
	 *
	 * @param  int   Total rows.
	 * @param  int   Records for each page.
	 * @param  int   Current offset or page.
	 * @param  int   Flag to set if 3rd parameter is offset or page.
	 * @return array Pager values.
	 */
	function getValues ($rows, $limit, $offset, $page_flag = FALSE, $show_pages = 10)
	{
		if ($page_flag) 
		{
			$results['pager']['page'] = $offset;
			
			if ($offset <= 1) {
				$offset = 0;
			} else {
				$offset = ($limit * $offset) - $limit; 
			}
		}
		else 
		{
			if ($offset == 0) {
				$results['pager']['page'] = 1;
			} else {
				$results['pager']['page'] = intval(($offset / $limit) + 1);
			}
		}
		
		$results['pager']['offset'] = $offset;
		$results['pager']['limit']  = $limit;
		
		$results['total_rows']   = $rows;
		$results['total_pages']  = ceil($rows / $limit);
		
		$results['first_record']  = $offset + 1;
		$results['last_record']   = $offset + $limit;
		
		if ($results['last_record'] > $rows) {
			$results['last_record'] = $rows;
		}
	
		$results['pager']['first'] = 0;
				
		$results['pager']['previous'] = $offset - $limit;
		
		if ($results['pager']['previous'] < 0) {
			$results['pager']['previous'] = 0;
		}
	
		$last = intval($rows / $limit) * $limit;
			
		$results['pager']['next'] = $offset + $limit;
		
		if ($results['pager']['next'] >= $rows) {
			$results['pager']['next'] = $last;
		}
	
		$results['pager']['last'] = $last;
		
		if ($results['pager']['last'] == $rows) {
			$results['pager']['last'] = $rows - $limit;
		}
		
		$first_page = $results['pager']['page'] - intval(($show_pages) / 2);
		$last_page = $results['pager']['page'] + intval(($show_pages - 1) / 2);
	
		if ($first_page < 1) 
		{
			$last_page += (-$first_page) + 1;
			$first_page = 1;
		}
	
		if ($last_page > $results['total_pages']) 
		{
			if ($show_pages < $results['total_pages']) {
				$first_page -= ($last_page - $results['total_pages']);
			}
			$last_page = $results['total_pages'];
		}
	
		for ($i = $first_page; $i <= $last_page; $i++) 
		{
			if ($page_flag) {
				$results['pages'][$i] = $i;
			} else {
				$results['pages'][$i] = ($limit * $i) - $limit;
			}
		}
	
		if ($page_flag) 
		{
			$results['pager']['first'] = 1;
			$results['pager']['previous'] = $offset / $limit;
			
			if ($results['pager']['previous'] == 0) {
				$results['pager']['previous'] = 1;
			}
			
			$results['pager']['next'] = $results['pager']['page'] + 1;
			
			if ($results['pager']['next'] > $results['total_pages']) {
				$results['pager']['next'] = $results['total_pages'];
			}
			
			$results['pager']['last'] = $results['total_pages'];
		}
		
		return $results;
	}

}

?>
