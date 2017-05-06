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

class zarinpal {
	static function send ($uniqId,$price,$port,$payDescription,$payerEmail,$payerMobile) {
		//------------------------ config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_zarinpal = $mconfig->loadPortSettings(3);
		//------------------------
		$app	= JFactory::getApplication();
		if ($port_zarinpal->terminal_code != null){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$session = JFactory::getSession();
			if($port_zarinpal->test_mode){
				$MerchantID = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'; 
			}
			else {
				$MerchantID = $port_zarinpal->terminal_code;
			}
			
			$Amount = ($price/10); //Amount will be based on Toman - Required
			$Description = $payDescription; // Required
			$Email = $payerEmail; // Optional
			$Mobile = $payerMobile; // Optional
			$CallbackURL = JURI::root().'index.php?option=com_tinypayment&view=form&layout=callback&task=form.callback'; // Required
			
			//set session of unid id 
			try {
				if ($port_zarinpal->test_mode != null && $port_zarinpal->test_mode == 1){
					$client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); // for local
				}
				else {
					$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); 	
				}

				$result = $client->PaymentRequest(
					[
					'MerchantID' => $MerchantID,
					'Amount' => $Amount,
					'Description' => $Description,
					'Email' => $Email,
					'Mobile' => $Mobile,
					'CallbackURL' => $CallbackURL,
					]
				);
				
				//Redirect to URL You can do it also by creating a form
				$resultStatus = abs($result->Status); // tabdli add manfi be mosbat
				if ($resultStatus == 100) {
					if ($port_zarinpal->test_mode != null && $port_zarinpal->test_mode == 1){
						Header('Location: https://sandbox.zarinpal.com/pg/StartPay/'.$result->Authority); // for local/
					}
					else {
						Header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority); 
					}
				} else {
					echo'ERR: '.$resultStatus;
				}
			}
			catch(\SoapFault $e) {
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
	
	static function verify ($uniqId,$portId,$price) {
		$app = JFactory::getApplication(); 
		$jinput = $app->input;
		//------------------------ config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_zarinpal = $mconfig->loadPortSettings(3);
		//------------------------
		$Authority = $jinput->get->get('Authority', '0', 'INT');
		$status = $jinput->get->get('Status', '', 'STRING');

		if ($port_zarinpal->terminal_code != null){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$session = JFactory::getSession();
			
			if (other::checkBot($uniqId)){	
				if($port_zarinpal->test_mode){
					$MerchantID = 'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'; 
				}
				else {
					$MerchantID = $port_zarinpal->terminal_code;
				}
				$Amount = $price/10; //Amount will be based on Toman

				if ($status == 'OK') {
					try {
						if ($port_zarinpal->test_mode != null && $port_zarinpal->test_mode == 1){
							$client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); // for local
						}
						else {
							$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); 
						}

						$result = $client->PaymentVerification(
						[
						'MerchantID' => $MerchantID,
						'Authority' => $Authority,
						'Amount' => $Amount,
						]
						);
						$resultStatus = abs($result->Status); // tabdli add manfi be mosbat
						if ($resultStatus == 100) {
							$msg= $Model->getTinyMsg($portId,$resultStatus); //get message from DB
							$Model->updateLogs($uniqId,$resultStatus,$msg); // update transcation logs
							$Model->updateTransactions($uniqId,$Authority,$result->RefID,''); // update transcation
							$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=callback',false);
							$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Message'); 
						} 
						else {
							$msg= $Model->getTinyMsg($portId,$resultStatus);
							$Model->updateLogs($uniqId,$resultStatus,$msg); // update transcation logs
							$Model->updateTransactions($uniqId,$Authority,$result->RefID,''); // update transcation
							$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
							$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
						}
					}
					catch(\SoapFault $e) {
						$app	= JFactory::getApplication();
						$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
						$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
					}
				} 
				else {
					$msg= $Model->getTinyMsg($portId,intval(17));
					$Model->updateLogs($uniqId,intval(17),$msg); // update transcation logs
					$Model->updateTransactions($uniqId,$Authority,0000000000,''); // update transcation
					$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
					$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
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
