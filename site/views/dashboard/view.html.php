<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// import Joomla view library
jimport('joomla.application.component.view');

class TinyPaymentViewDashboard extends JViewLegacy
{
	// Overwriting JView display method
	function display($tpl = null) 
	{
		$app		= JFactory::getApplication();
		$params		= $app->getParams();
		$dispatcher = JDispatcher::getInstance();
 
		// Get some data from the models
		$state		= $this->get('State');
		$item		= $this->get('Item');
		$this->form	= $this->get('Form');
 		$params 		= JComponentHelper::getParams('com_tinypayment');
		$this->params   = $params->toArray();
		// Check for errors.
		if (count($errors = $this->get('Errors'))) 
		{
			JFactory::getApplication()->enqueueMessage(JText::_($errors), 'error');
			return false;
		}
		
		$model = $this->getModel('dashboard');
		$total = count($model->getTotal());
		$result = TinyPaymentHelper::pagination($total);
		$this->assignRef('pagination',$result);
		

		// Display the view
		parent::display($tpl);
	}

	
}
