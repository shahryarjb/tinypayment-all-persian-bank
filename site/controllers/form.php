<?php 
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'tinypayment.php'; 
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'inputcheck.php'; 
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'otherport.php'; 

class TinyPaymentControllerForm extends JControllerForm
{

	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
 
	public function submit()
	{

		// Check for request forgeries.
		JSession::checkToken( 'post' ) or die( 'Invalid Token' );
 
		// Initialise variables.
		$app	= JFactory::getApplication();
		$model	= $this->getModel('form');
		
		$jinput = JFactory::getApplication()->input;
		$input = $jinput->getArray(array(
							'jform' => array(
								'payer_name' => 'STRING',
								'payer_mobile' => 'STRING',
								'payer_email' => 'STRING',
								'pay_title' => 'STRING',
								'pay_description' => 'STRING',
								'pay_price' => 'INT',
								'payer_ip' => 'STRING',
								'pay_port' => 'INT'
								)
						));
		
		//========================================================= captcha
		$remoteip  = other::getRealIpAddr();
		if ($app->getParams()->get('recapstatus') != null && $app->getParams()->get('recapstatus') == 1) {
			$privatekey = $app->getParams()->get('prerecapcod');
			$response = $jinput->get('g-recaptcha-response','','string');
			require_once JPATH_SITE.'/plugins/captcha/recaptcha/recaptchalib.php';

			$reCaptcha = new JReCaptcha($privatekey);
			$response  = $reCaptcha->verifyResponse($remoteip, $response);
			
			if ( !isset($response->success) || !$response->success) {
				if ( $response->success != 1 ){
					$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
					$app->redirect($link, '<h2>.کد امنیتی اشتباه می باشد.</h2>', $msgType='Error'); 
				}
			}
		}
		//========================================================= captcha		
		//-------------------------------- variable
		$payTitle = $input['jform']['pay_title'];
		$payDescription = $input['jform']['pay_description'];
		$payerName = $input['jform']['payer_name'];
		$payerMobile = $input['jform']['payer_mobile'];
		$payerEmail = $input['jform']['payer_email'];
		$payerIp = $input['jform']['payer_ip'];
		$port = $input['jform']['pay_port'];
		//---------------
		$createTime = time();
		//---------------
		$price = $input['jform']['pay_price'];
		//------------- redirect
		if (
			checkHack::checkString($payTitle) && 
			checkHack::checkAlphaNumberic($payDescription) &&  
			checkHack::checkString($payerName) && 
			checkHack::checkMobile($payerMobile) &&  
			checkHack::checkEmail($payerEmail) &&  
			checkHack::checkString($payerIp) && 
			checkHack::checkNum($createTime) && 
			checkHack::checkNum($price) &&  
			checkHack::checkNum($port) 	
		) {
			$model->sendpay($payTitle,$payDescription,$payerName,$payerMobile,$payerEmail,$payerIp,$createTime,$price,$port);
		}
		else {
			$data = array($payTitle,$payDescription,$payerName,$payerMobile,$payerEmail,$payerIp,$createTime,$price,$port);
			$newData = checkHack::joinStrip($data);
			other::reqForm($newData,'hck2');
		}	
	}
	
	
	public function callback () {
		$model	= $this->getModel('form');
		$model->callback2();
	}
	
	public function pdf2() {
		JSession::checkToken( 'post' ) or die( 'Invalid Token' );
		$jinput = JFactory::getApplication()->input;
		$data = $jinput->get('id', '', 'string');
		$app	= JFactory::getApplication();
		$app-> setHeader('Content-Type', 'application/pdf; charset=utf-8', true);
		$model	= $this->getModel('form');
		$model->CallPdf($data);
		$app->close();	
	}
 
}
