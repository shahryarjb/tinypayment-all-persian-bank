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

class TinyPaymentViewOutputs extends JViewLegacy
{
	public $comName = "tinypayment"; 
	
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;


	public function display($tpl = null)	{
		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->script = $this->get('Script');
		$this->canDo = TinyPaymentHelper::getActions($this->item->id);
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode('<br />', $errors));

			return false;
		}
		$this->addToolBar();
		parent::display($tpl);



	}

	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
		$isNew = ($this->item->id == 0);
		JToolBarHelper::title($isNew ? JText::_('آمار') : JText::_('COM_DASHBOARD_CLASS_EDIT'), $this->comName);
		if ($this->canDo->get('core.admin')) {
			JToolBarHelper::divider();
			JToolBarHelper::preferences('com_tinypayment');
		}
	}

}
