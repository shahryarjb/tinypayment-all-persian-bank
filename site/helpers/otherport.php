<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport( 'joomla.application.application' );
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_tinypayment/models'); 

class other {
	static function test($uniqId) {
		$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );	
		$app	= JFactory::getApplication();
		$session = JFactory::getSession();
		if ($session->isActive('uniqId')) { $session->clear('uniqId'); }
		$msg= $Model->getTinyMsg(0,'empty'); //get message from DB
		$Model->updateLogs($uniqId,'empty',$msg); // update transcation logs
		$Model->updateTransactions($uniqId,'','',''); // update transcation
		$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
		$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
	}	
	
	static function hack($uniqId) {
		$session = JFactory::getSession();
		$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );	
		$app	= JFactory::getApplication();
		if ($session->isActive('uniqId')) { $session->clear('uniqId'); }
		$msg= $Model->getTinyMsg(0,'hck3'); //get message from DB
		$Model->updateLogs($uniqId,'hck3',$msg); // update transcation logs
		$Model->updateTransactions($uniqId,'','',''); // update transcation
		$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
		$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
	}
	
	static function reqForm ($data,$msg) {
		$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );	
		$remoteip  = other::getRealIpAddr();
		$user_ip = $remoteip;
		$user = JFactory::getUser();
		$user_id = $user->id;
		
		$app	= JFactory::getApplication();
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$columns = array('user_id','user_ip','input_string');
		$values =  array($db->q($user_id),$db->q($user_ip),$db->q($data));		
		$query->insert($db->qn('#__tinypayment_form_logs'));
		$query->columns($db->qn($columns));
		$query->values(implode(',',$values)); 
		$db->setQuery((string)$query); 
		$db->execute(); 
		$msg= $Model->getTinyMsg(0,$msg); //get message from DB
		$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
		$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
		// inja mishe email kar be admin
	}
	
	static function getRefNum ($refId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('ref_id')
			->from($db->qn('#__tinypayment_transactions'));
		$query->where($db->qn('ref_id') . ' = ' . $db->q($refId) );
		$db->setQuery((string)$query); 
		$result = $db->loadResult();
		return $result;
	}
	
	static function checkBot ($uniqId) {
		$app	= JFactory::getApplication();
		$remoteip  = other::getRealIpAddr();
		$userIp = $remoteip;
		if ($app->getParams()->get('backtime') != null )
			$newTime = ($app->getParams()->get('backtime')) * 60;
		else 
			$newTime = 11*60; //jaye in 10 mishe goft meghdar az config gerefte beshe
		
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('payer_ip,create_time')
			->from($db->qn('#__tinypayment_paymentinfo'));
		$query->where($db->qn('uniq') . ' = ' . $db->q($uniqId));
		$db->setQuery((string)$query); 
		$result = $db->loadAssoc();
								
		if (($result['payer_ip'] == $userIp) && (($result['create_time']) + $newTime >= time()))   
				return true;
			else 
				return false; 	
	}

	static function getRealIpAddr() {
		if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
			$ip=$_SERVER['HTTP_CLIENT_IP'];
		}
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
			$ip=$_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		else{
			$ip=$_SERVER['REMOTE_ADDR'];
		}
		return $ip;
	}
}
?>
