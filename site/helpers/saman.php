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
require_once JPATH_SITE .'/components/com_tinypayment/helpers/otherport.php'; 
require_once JPATH_SITE .'/components/com_tinypayment/helpers/inputcheck.php'; 
//---------------------------------------- config 
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php'; 
//----------------------------------------

class saman {
	static function send ($uniqId,$price,$port) {
		//------------------------ config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_saman = $mconfig->loadPortSettings(9);
		//------------------------
		$app	= JFactory::getApplication();
		if (
			$port_saman->terminal_code != null && $port_saman->terminal_code != 'terminalcode' && 
			$port_saman->username != null && $port_saman->username != 'username' &&
			$port_saman->password !=null && $port_saman->password != 'password'
			){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$session = JFactory::getSession();
			$merchantId =$port_saman->terminal_code;
			$reservationNumber = time();
			$totalAmount = $price;
			$callBackUrl  = JURI::root().'index.php?option=com_tinypayment&view=form&layout=callback&task=form.callback';
			$sendUrl = "https\://sep.shaparak.ir/Payment.aspx";
			
				echo '
					<script>
						var form = document.createElement("form");
						form.setAttribute("method", "POST");
						form.setAttribute("action", "'.$sendUrl.'");
						form.setAttribute("target", "_self");

						var hiddenField1 = document.createElement("input");
						hiddenField1.setAttribute("name", "Amount");
						hiddenField1.setAttribute("value", "'.$totalAmount.'");
						form.appendChild(hiddenField1);
						
						var hiddenField2 = document.createElement("input");
						hiddenField2.setAttribute("name", "MID");
						hiddenField2.setAttribute("value", "'.$merchantId.'");
						form.appendChild(hiddenField2);
						
						var hiddenField3 = document.createElement("input");
						hiddenField3.setAttribute("name", "ResNum");
						hiddenField3.setAttribute("value", "'.$reservationNumber.'");
						form.appendChild(hiddenField3);
						
						var hiddenField4 = document.createElement("input");
						hiddenField4.setAttribute("name", "RedirectURL");
						hiddenField4.setAttribute("value", "'.$callBackUrl.'");
						form.appendChild(hiddenField4);
						

						document.body.appendChild(form);
						form.submit();
						document.body.removeChild(form);
					</script>'
				;
		}
		else {
			$app	= JFactory::getApplication();
			$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
			$app->redirect($link, '<h2>یک یا چند پیکربندی از افزونه بدرستی انجام نشده است.</h2>', $msgType='Error'); 
		}
		
	}
	
	static function verify ($cryptId,$portId) {
		$app = JFactory::getApplication(); 
		$jinput = $app->input;
		//------------------------ config
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		$port_saman = $mconfig->loadPortSettings(9);
		//------------------------
		if (
			$port_saman->terminal_code != null && $port_saman->terminal_code != 'terminalcode' && 
			$port_saman->username != null && $port_saman->username != 'username' &&
			$port_saman->password !=null && $port_saman->password != 'password'
		){
			$Model = JModelLegacy::getInstance( 'Form', 'TinyPaymentModel' );
			$getData = $Model->getPaymentInfo($cryptId)[0];
			$uniqId = $getData->uniq;
			$price = $getData->price;
			if (other::checkBot($uniqId)){	
				$merchantId =$port_saman->terminal_code;
				$resNum = $jinput->post->get('ResNum', '0', 'INT');
				$trackingCode = $jinput->post->get('TRACENO', '0', 'INT');
				$stateCode = $jinput->post->get('stateCode', '1', 'INT');
				$refNum = $jinput->post->get('RefNum', 'empty', 'STRING');
				if (checkHack::strip($refNum) != $refNum )
					$refNum = "illegal";
				$state = $jinput->post->get('State', 'empty', 'STRING');
				if (checkHack::strip($state) != $state )
					$state = "illegal";
				$cardNumber = $jinput->post->get('SecurePan', 'empty', 'STRING'); 
				if (checkHack::strip($cardNumber) != $cardNumber )
					$cardNumber = "illegal";	

					if (isset($state) && ($state == 'OK' || $stateCode == 0)) {
						try {
							$out    = new SoapClient('https://sep.shaparak.ir/payments/referencepayment.asmx?WSDL');
							$resultCode    = $out->VerifyTransaction($refNum, $merchantId);
						
							if ($resultCode == round($price,2)) {
								$msg= $Model->getTinyMsg($portId,1); //get message from DB
								$Model->updateLogs($uniqId,1,$msg); // update transcation logs
								$Model->updateTransactions($uniqId,$resNum,$trackingCode,$cardNumber); // update transcation
								$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=callback',false);
								$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Message'); 
							}
							else {
								$msg= $Model->getTinyMsg($portId,$state); //get message from DB
								$Model->updateLogs($uniqId,$resultCode,$msg); // update transcation logs
								$Model->updateTransactions($uniqId,$resNum,$refNum,''); // update transcation
								$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
								$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
							}
						}
						catch(\SoapFault $e)  {
							$app	= JFactory::getApplication();
							$link = JRoute::_('index.php?option=com_tinypayment&view=form&layout=default',false);
							$app->redirect($link, '<h2>خطا غیر منتظره رخ داده است</h2>', $msgType='Error'); 
						}
					}
					else {
						$msg= $Model->getTinyMsg($portId,$state);
						$Model->updateLogs($uniqId,$state,$msg); // update transcation logs
						$Model->updateTransactions($uniqId,$resNum,$refNum,''); // update transcation
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
