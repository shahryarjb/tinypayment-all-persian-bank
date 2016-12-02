<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');


class TinyPaymentModelstorepayments extends JModelList
{

	public function __construct($config = array())
	{
		if (empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id' , 'p.id',
				'pay_title', 'p.pay_title',
				'pay_description', 'p.pay_description',
				'pay_amount', 'p.pay_amount',
				'pay_port', 'p.pay_port',
				'pay_time', 'p.pay_time',
				'payer_name', 'p.payer_name',
				'payer_mobile', 'p.payer_mobile',
				'payer_email', 'p.payer_email',
				'payer_ip', 'p.payer_ip',
				'order_status', 'p.order_status',
				'tracking_code', 'pt.tracking_code',
				
			);
		}

		parent::__construct($config);
	}
//------------------------------------------

protected function populateState($ordering = 'p.id', $direction = 'desc')
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

	protected function getListQuery()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($this->getState('list.select','
				p.id,p.pay_title,p.payer_name,p.payer_mobile,p.payer_email,p.order_status,
				pt.port_id as pay_port,pt.price as pay_amount,pt.last_change_date as pay_time,pt.tracking_code,
				psl.result_message as pay_status'
		));
		$query->from($db->qn('#__tinypayment_paymentinfo') . ' as p');
		$query->leftJoin($db->qn('#__tinypayment_transactions') . ' as pt ON pt.id = p.id');
		$query->leftJoin($db->qn('#__tinypayment_status_log') . ' as psl ON psl.transaction_id = pt.id');

		$search = $this->getState('filter.search');
		if (!empty($search))
		{
			$like = $db->quote('%' . $search . '%');
			$query->where('pt.tracking_code LIKE ' . $like);
		}

		$published = $this->getState('filter.published');

		$orderCol	= $this->state->get('list.ordering', 'id');
		$orderDirn 	= $this->state->get('list.direction', 'asc');

		$query->order($db->escape($orderCol) . ' ' . $db->escape($orderDirn));

		return $query;
	}
	
	public function portName ($id) {
		switch($id) {
			case 0:$out = "بدون درگاه";break;
			case 1:$out = "ملت";break;
			case 2:$out = "ملی";break;
			case 3:$out = "زرین پال";break;
			case 4:$out = "پی لاین";break;
			case 5:$out = "جهان پی";break;
			case 6:$out = "پارسیان";break;
			case 7:$out = "پاسارگاد";break;
			case 8:$out = "صادرات";break;
			case 9:$out = "سامان";break;
			default:$out= "بدون درگاه";break;
		}
		return $out;	
	}
	
	public function orderStatus ($id) {
		switch($id) {
			case 1:$out = "کنسل شده";break;
			case 2:$out = "کنسل نشده";break;
			case 3:$out = "برگشت مبلغ";break;
			case 4:$out = "انجام شده";break;
			case 5:$out = "رد کردن";break;
			case 6:$out = "منقضی شده";break;
			case 7:$out = "ناموفق";break;
			case 8:$out = "معلق";break;
			case 9:$out = "بررسی شده";break;
			case 10:$out = "در حال بررسی";break;
			case 11:$out = "پرداخت مجدد";break;
			case 12:$out = "برگشت";break;
			case 13:$out = "پست شده";break;
			case 14:$out = "باطل شده";break;
			default:$out ="بررسی نشده";
		}
		return $out;	
	}
	
	public function convert_date_to_unix($date_time) {
    		// Get the User and their timezone
		    	$user = JFactory::getUser();
		    	$timeZone = $user->getParam('timezone', 'UTC');

	    	// Create JDate object set to now in the users timezone.
	    	    $myDate = JDate::getInstance($date_time, $timeZone);

	    		return $myDate->toUnix();
	}
	
	public function statistic($i) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('id')
		  ->from($db->qn('#__tinypayment_status_log'));
		if ($i == 'ok')
			$query->where(
				$db->qn('result_code') . ' = ' . intval(0) 
				. ' OR '.
				$db->qn('result_code') . ' = ' . intval(1)
				. ' OR '.
				$db->qn('result_code') . ' = ' . intval(100)
			);
		$db->setQuery($query);
		$row = $db->loadObjectlist();
		return $row;
	}
	
	
}
