<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/jdf.php';

class TinyPaymentModelSettings extends JModelAdmin {
	
	
	public function getTable($type = 'Settings', $prefix = 'TinyPaymentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		
	}

//=====================================================================
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(
			'com_tinypayment.edit.section.data',
			array()
		);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
//=====================================================================
	protected function canDelete($record) {
		if( !empty( $record->id ) ) {
			return JFactory::getUser()->authorise( "core.delete", "com_tinypayment.message." . $record->id );
		}
	}
	

//------------------------------------------

	protected function populateState($ordering = 'id', $direction = 'desc')
		{
			// List state information.
			parent::populateState($ordering, $direction);
		}

//------------------------------------------
	protected function getStoreId($id = '')
		{
			return parent::getStoreId($id);
		}
//------------------------------------------
//============================================ main_setttings
	function updateMainSettings ($data) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->qn('time_back') . ' = ' . $db->q($data->time_back),
			$db->qn('show_pdf') . ' = ' . $db->q($data->show_pdf),
			$db->qn('captcha') . ' = ' . $db->q($data->captcha),
			$db->qn('public_key') . ' = ' . $db->q($data->public_key),
			$db->qn('private_key') . ' = ' . $db->q($data->private_key),
			$db->qn('show_email') . ' = ' . $db->q($data->show_email),
			$db->qn('bootstrap') . ' = ' . $db->q($data->bootstrap)
		);
		
		$conditions = array($db->qn('id') . ' = 1');
		$query->update($db->qn('#__tinypayment_settings'))->set($fields)->where($conditions);
		$db->setQuery((string)$query); 
		$db->execute();
		return true; 
	}
//=============================================
//============================================ port_settings
	function updatePortSettings ($data) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$fields = array(
			$db->qn('active') . ' = ' . $db->q($data['active']),
			$db->qn('username') . ' = ' . $db->q($data['username']),
			$db->qn('password') . ' = ' . $db->q($data['password']),
			$db->qn('terminal_code') . ' = ' . $db->q($data['terminal_code']),
			$db->qn('test_mode') . ' = ' . $db->q($data['test_mode'])
		);
		
		$conditions = array($db->qn('bank_id') . ' = ' . $db->q($data['bankid']));
		$query->update($db->qn('#__tinypayment_banks'))->set($fields)->where($conditions);
		$db->setQuery((string)$query); 
		$db->execute();
		return true; 
	}
//=============================================
}
