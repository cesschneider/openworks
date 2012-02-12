<?php

//-------------------------------------------------------------------------
// OpenWorks 1.0 - Open Source PHP Application Framework.
// Copyright(c) 2005 Cesar Schneider.
//
// For the full copyright and license information, please view the
// COPYRIGHT and LICENCE files that was distributed with this source code.
//-------------------------------------------------------------------------

/**
 * Controller.class.php - Implements the Front Controller design pattern.
 *
 * This class controls all action process and presentation.
 *
 * @author     Cesar Schneider <cesschneider at users sf dot net>
 * @package    openworks
 * @subpackage core
 * @version    1.0
 */

require OPENWORKS_CORE_DIR .'Action.class.php';
require OPENWORKS_CORE_DIR .'Dao.class.php';
require OPENWORKS_CORE_DIR .'Request.class.php';
require OPENWORKS_CORE_DIR .'Filter.class.php';
require OPENWORKS_CORE_DIR .'FilterChain.class.php';
require OPENWORKS_CORE_DIR .'Model.class.php';
require OPENWORKS_CORE_DIR .'View.class.php';

/**
 * class Controller
 *
 * @package openworks
 */
class Controller
{
	/**
	 * Dispatches the user request, pass control to Action class and render output.
	 */
	function dispatch ()
	{
		Message::info('Starting Controller');
	
		$filter  = &FilterChain::singleton();
		$request = &Request::singleton();
		$view    = &View::singleton('html');
		
		$request->parseUserRequest();

		// reset action count
		$action_count = 0;

		do
		{
			Message::debug('Action count: '. $action_count);

			// set forward request flag
			$request->forward_request = FALSE;

			// update action trace array
			$request->updateActionTrace();

			// check if action exists
			if ($request->checkActionRequest())
			{
				Message::debug('Executing pre action filters');
	
				// execute pre action filters
				$filter->executeFilters($request->action_request, FILTER_PRE_ACTION_TYPE);


				if (! $request->forward_request)
				{
					// require action class file
					$request->getActionFile();
					require_once $request->getActionFile();
		
					// instantiate and execute action class
					$class_name = $request->getActionClass();
					$action = new $class_name;
		
					Message::debug('Executing action: '. $request->action_request);
					$action->execute($request, $view);
			
					Message::debug('Executing post action filters');
		
					// execute post action filters
					$filter->executeFilters($request->action_request, FILTER_POST_ACTION_TYPE);
				}
			}

			// if action doesn't exists
			else
			{
				Message::debug('Forwarding to error action');
				$request->forwardRequest(REQUEST_ERROR_ACTION);
			}

			// increment action count
			$action_count++;

	} while ($request->forward_request && $action_count <= REQUEST_FORWARD_LIMIT);

	/*
		$view->assign('request', $request->getAttributes());

		if ( file_exists(APPLICATION_TEMPLATE_DIR .'document.html.tpl') ) {
			$view->setTemplateFile('document.html.tpl');
		} else if ( file_exists(APPLICATION_TEMPLATE_DIR .'document.tpl') ) {
			$view->setTemplateFile('document.tpl');
		}
	*/
	
		$view->display();

		Message::openConsoleWindow();

	//	Util::htmlDump($request);
	//	Util::htmlDump($filter);
	//	Util::htmlDump($_SESSION, 'SESSION');
	//	Util::htmlDump(Message::getElapsedTime(), 'Total execution time: ');
	}
}

?>