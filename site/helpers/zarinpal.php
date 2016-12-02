<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;
jimport('joomla.application.component.model');
JModelLegacy::addIncludePath(JPATH_SITE.'/components/com_tinypayment/models'); 
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'otherport.php'; 

class zarinpal {
	static function send ($uniqId,$price,$port,$payDescription,$payerEmail,$payerMobile) {
		$app	= JFactory::getApplication();
		if ($app->getParams()->get('zarinpalmerchantid') != null){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$session = JFactory::getSession();
			$MerchantID = $app->getParams()->get('zarinpalmerchantid');//'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'; //Required
			$Amount = ($price/10); //Amount will be based on Toman - Required
			$Description = $payDescription; // Required
			$Email = $payerEmail; // Optional
			$Mobile = $payerMobile; // Optional
			$CallbackURL = JURI::root().'index.php?option=com_tinypayment&view=form&layout=callback&task=form.callback'; // Required
			
			//set session of unid id 
			try {
				$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); 	
				//$client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); // for local

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
				
				Header('Location: https://www.zarinpal.com/pg/StartPay/'.$result->Authority); 
				//Header('Location: https://sandbox.zarinpal.com/pg/StartPay/'.$result->Authority); // for local/
				} else {
					echo'ERR: '.$resultStatus;
				}
			}
			catch(\SoapFault $e) {
				$app	= JFactory::getApplication();
				$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
				$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
			}
		}
		else {
			$app	= JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
			$app->redirect($link, '<h2>یک یا چند پیکربندی از افزونه بدرستی انجام نشده است.</h2>', $msgType='Error'); 
		}
	}
	
	static function verify ($uniqId,$portId,$price) {
		$app	= JFactory::getApplication();
		if ($app->getParams()->get('zarinpalmerchantid') != null){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$session = JFactory::getSession();
			
			if (other::checkBot($uniqId)){	
				$MerchantID = $app->getParams()->get('zarinpalmerchantid');//'XXXXXXXX-XXXX-XXXX-XXXX-XXXXXXXXXXXX'; //Required
				$Amount = $price/10; //Amount will be based on Toman
				$Authority = $_GET['Authority'];

				if ($_GET['Status'] == 'OK') {
					try {
						$client = new SoapClient('https://www.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); 
						//$client = new SoapClient('https://sandbox.zarinpal.com/pg/services/WebGate/wsdl', ['encoding' => 'UTF-8']); // for local

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
							$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
							$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
						}
					}
					catch(\SoapFault $e) {
						$app	= JFactory::getApplication();
						$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
						$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
					}
				} 
				else {
					$msg= $Model->getTinyMsg($portId,intval(17));
					$Model->updateLogs($uniqId,intval(17),$msg); // update transcation logs
					$Model->updateTransactions($uniqId,$Authority,0000000000,''); // update transcation
					$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
					$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
				}
			}
			else {
				other::hack($uniqId);
			}
		}
		else {
			$app	= JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
			$app->redirect($link, '<h2>یک یا چند پیکربندی از افزونه بدرستی انجام نشده است.</h2>', $msgType='Error'); 
		}
	}
}

?>
