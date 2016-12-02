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

class TinyPaymentViewStorePayment extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $script;
	protected $canDo;

	public function display($tpl = null)
	{
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
		$this->setDocument();
	}

	protected function addToolBar()
	{
		$input = JFactory::getApplication()->input;
		$input->set('hidemainmenu', true);
		$isNew = ($this->item->id == 0);
		JToolBarHelper::title($isNew ? JText::_('COM_MINIUNIVERSITY_MANAGER_TEACHER_NEW')
		                             : JText::_('صفحه نمایش فاکتور'), 'tinypayment');
		if ($isNew)
		{
			if ($this->canDo->get('core.create')) 
			{
				JToolBarHelper::apply('storepayment.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('storepayment.save', 'JTOOLBAR_SAVE');
				
			}
			JToolBarHelper::cancel('storepayment.cancel', 'JTOOLBAR_CANCEL');
		}
		else
		{
			if ($this->canDo->get('core.edit'))
			{
				JToolBarHelper::apply('storepayment.apply', 'JTOOLBAR_APPLY');
				JToolBarHelper::save('storepayment.save', 'JTOOLBAR_SAVE');
 
			}
		
			JToolBarHelper::cancel('storepayment.cancel', 'JTOOLBAR_CLOSE');
		}
		
		JToolBarHelper::custom('storepayment.csv2', 'download','csv','csv',false);
		JToolBarHelper::custom('storepayment.pdf2', 'pdfcoustom','pdf','pdf',false);		
	}
	
	protected function setDocument() 
	{
		$isNew = ($this->item->id == 0);
		$document = JFactory::getDocument();
		$document->setTitle($isNew ? JText::_('COM_MINIUNIVERSITY_TEACHER_CREATING')
		                           : JText::_('نمایش اطلاعات فاکتور کاربر'));
	}
}
