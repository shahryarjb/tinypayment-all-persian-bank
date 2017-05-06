<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_tinypayment/models'); 
require_once JPATH_SITE .'/components/com_tinypayment/helpers/otherport.php'; 
require_once JPATH_SITE .'/components/com_tinypayment/helpers/inputcheck.php'; 
//---------------------------------------- config 
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php'; 
//----------------------------------------

class mellat {
	static function send ($uniqId,$price,$port) {
		//---------------------------------------- config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_mellat = $mconfig->loadPortSettings(1);
		//----------------------------------------
		$app	= JFactory::getApplication();
		if (
			$port_mellat->terminal_code != null && $port_mellat->terminal_code != 'terminalcode' && 
			$port_mellat->username != null && $port_mellat->username != 'username' &&
			$port_mellat->password !=null && $port_mellat->password != 'password'
			){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );	
			$session = JFactory::getSession();
			$dateTime = JFactory::getDate();
			$fields = array(
			'terminalId' => $port_mellat->terminal_code,
			'userName' => $port_mellat->username,
			'userPassword' => $port_mellat->password,
			'orderId' => time(),
			'amount' => $price,
			'localDate' => $dateTime->format('Ymd'),
			'localTime' => $dateTime->format('His'),
			'additionalData' => '',
			'callBackUrl' => JURI::root().'index.php?option=com_tinypayment&view=form&layout=callback&task=form.callback',
			'payerId' => 0,
			);
			
			try {
				$soap = new SoapClient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
				$response = $soap->bpPayRequest($fields);
				
				$response = explode(',', $response->return);
				if ($response[0] != '0') { // if transaction fail
					$msg = $Model->getTinyMsg($port,$response[0]); //get message from DB	
					$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
					$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
				}
				else { // if success
					$refId = $response[1];
					echo '
						<script>
							var form = document.createElement("form");
							form.setAttribute("method", "POST");
							form.setAttribute("action", "https://bpm.shaparak.ir/pgwchannel/startpay.mellat");
							form.setAttribute("target", "_self");

							var hiddenField = document.createElement("input");
							hiddenField.setAttribute("name", "RefId");
							hiddenField.setAttribute("value", "'.$refId.'");

							form.appendChild(hiddenField);

							document.body.appendChild(form);
							form.submit();
							document.body.removeChild(form);
						</script>'
					;
				}
			}
			catch(\SoapFault $e)  {
				$app	= JFactory::getApplication();
				$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
				$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
			}
		}
		else {
			$app	= JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
			$app->redirect($link, '<h2>یک یا چند پیکربندی از افزونه بدرستی انجام نشده است.</h2>', $msgType='Error'); 
		}
	}
	
	static function verify ($uniqId,$portId) {
		$app = JFactory::getApplication(); 
		$jinput = $app->input;
		//---------------------------------------- config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_mellat = $mconfig->loadPortSettings(1);
		//----------------------------------------
		$app	= JFactory::getApplication();
		if (
			$port_mellat->terminal_code != null && $port_mellat->terminal_code != 'terminalcode' && 
			$port_mellat->username != null && $port_mellat->username != 'username' &&
			$port_mellat->password !=null && $port_mellat->password != 'password'
		){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );	
			$session = JFactory::getSession();
			if (other::checkBot($uniqId)){	
				$ResCode = $jinput->post->get('ResCode', '1', 'INT'); 
				$SaleOrderId = $jinput->post->get('SaleOrderId', '1', 'INT'); 
				$SaleReferenceId = $jinput->post->get('SaleReferenceId', '1', 'INT'); 
				$RefId = $jinput->post->get('RefId', 'empty', 'STRING'); 
				if (checkHack::strip($RefId) != $RefId )
					$RefId = "illegal";
				$CardNumber = $jinput->post->get('CardHolderPan', 'empty', 'STRING'); 
				if (checkHack::strip($CardNumber) != $CardNumber )
					$CardNumber = "illegal";
				

				if ($ResCode != '0') {
					$msg= $Model->getTinyMsg($portId,$ResCode); //get message from DB
					$Model->updateLogs($uniqId,$ResCode,$msg); // update transcation logs
					$Model->updateTransactions($uniqId,$RefId,$SaleReferenceId,$CardNumber); // update transcation
					$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
					$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
				}
				else {
					$fields = array(
					'terminalId' => $port_mellat->terminal_code,
					'userName' => $port_mellat->username,
					'userPassword' => $port_mellat->password,
					'orderId' => $SaleOrderId, 
					'saleOrderId' =>  $SaleOrderId, 
					'saleReferenceId' => $SaleReferenceId //trackingCode
					);
					try {
						$soap = new SoapClient('https://bpm.shaparak.ir/pgwchannel/services/pgw?wsdl');
						$response = $soap->bpVerifyRequest($fields);

						if ($response->return != '0') {
							$msg= $Model->getTinyMsg($portId,$response->return); //get message from DB
							$Model->updateLogs($uniqId,$response->return,$msg); // update transcation logs
							$Model->updateTransactions($uniqId,$RefId,$SaleReferenceId,$CardNumber); // update transcation
							$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
							$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
						}
						else {	

							$response = $soap->bpSettleRequest($fields);
							
							if ($response->return == '0' || $response->return == '45') {
								$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=callback',false);
								$msg= $Model->getTinyMsg($portId,$response->return); //get message from DB
								$Model->updateLogs($uniqId,$response->return,$msg); // update transcation logs
								$Model->updateTransactions($uniqId,$RefId,$SaleReferenceId,$CardNumber); // update transcation
								$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Message'); 
							}
							else {
								$msg= $Model->getTinyMsg($portId,$response->return); //get message from DB
								$Model->updateLogs($uniqId,$response->return,$msg); // update transcation logs
								$Model->updateTransactions($uniqId,$RefId,$SaleReferenceId,$CardNumber); // update transcation
								$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
								$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
							}
						}
					}
					catch(\SoapFault $e)  {
						$app	= JFactory::getApplication();
						$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
						$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
					}
				}
			}
			else {
				other::hack($uniqId);
			}
		}
		else {
			$app	= JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
			$app->redirect($link, '<h2>یک یا چند پیکربندی از افزونه بدرستی انجام نشده است.</h2>', $msgType='Error'); 
		}
	}
	
}

?>
