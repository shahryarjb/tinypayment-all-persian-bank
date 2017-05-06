<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class TinyPaymentViewGuide extends JViewLegacy
{
	public $comName = "tinypayment"; 
	
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;

	public function display($tpl = null)	{
		// Get data from the model
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		
		// // Get the Data
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');

		// Check for errors.
		if (count($errors = $this->get('Errors')))
		{
			JFactory::getApplication()->enqueueMessage(JText::_($errors), 'error');
			return false;
		}

		$this->addToolBar();

		// Display the template
		parent::display($tpl);



	}

	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
		JToolBarHelper::title(JText::_('راهنما'), $this->comName);
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_tinypayment');
	}

}
