<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

if (!class_exists ('checkHack')) { 
	require_once JPATH_SITE .'/components/com_tinypayment/helpers/inputcheck.php'; 
}

class TinyPaymentControllerSettings extends JControllerForm {

	protected function allowAdd($data = array()) {
		return parent::allowAdd($data);
	}

	protected function allowEdit($data = array(), $key = 'id') {
		$id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) ) {
			return JFactory::getUser()->authorise( "core.edit", "com_tinypayment.message." . $id );
		}
	}
	
	public function backbutton() {
		$app = JFactory::getApplication();
		$url = JRoute::_('index.php?option=com_tinypayment&view=Settings', false);
		$app->redirect($url);
	}

	public function updateAllSettings() {
		$app = JFactory::getApplication('site');
		$jinput = $app->input;
		$input = $jinput->getArray(array(
			'jform' => array(
					'time_back' => 'INT',
					'show_pdf' => 'INT',
					'captcha' => 'INT',
					'public_key' => 'STRING',
					'private_key' => 'STRING',
					'show_email' => 'INT',
					'bootstrap' => 'INT',
					'active9' => 'INT',
					'username9' => 'STRING',
					'password9' => 'STRING',
					'terminalcode9' => 'STRING',
					'active1' => 'INT',
					'username1' => 'STRING',
					'password1' => 'STRING',
					'terminalcode1' => 'STRING',
					'active3' => 'INT',
					'testmode3' => 'INT',
					'terminalcode3' => 'STRING'
					)
			));
		

		$data = new stdClass();
		$port = new stdClass();
		//------------------------------------------
						// main value
		//------------------------------------------
		$data->time_back = $input['jform']['time_back'];
		$data->show_pdf = $input['jform']['show_pdf'];
		$data->captcha = $input['jform']['captcha'];
		$data->public_key = $input['jform']['public_key'];
		$data->private_key = $input['jform']['private_key'];
		$data->show_email = $input['jform']['show_email'];
		$data->bootstrap = $input['jform']['bootstrap'];
		//------------------------------------------
						// ports value
		//------------------------------------------
		$saman_active = $input['jform']['active9'];
		$saman_terminal = $input['jform']['terminalcode9'];
		$port->saman = array(
				'bankid' => 9 , 
				'active' => $saman_active, 
				'terminal_code' =>$saman_terminal ,
				'test_mode' => 0
			);
		//------------------------------------------
		$mellat_active = $input['jform']['active1'];
		$mellat_user = $input['jform']['username1'];
		$mellat_pass = $input['jform']['password1'];
		$mellat_terminal = $input['jform']['terminalcode1'];
		$port->mellat = array(
				'bankid' => 1 , 
				'active' => $mellat_active, 
				'username' =>$mellat_user ,
				'password' =>$mellat_pass , 
				'terminal_code' =>$mellat_terminal,
				'test_mode' => 0
			);
		//------------------------------------------
		$zarinpal_active = $input['jform']['active3'];
		$zarinpal_user = 'username';
		$zarinpal_pass = 'password';
		$zarinpal_terminal = $input['jform']['terminalcode3'];
		$zarinpal_test = $input['jform']['testmode3'];
		$port->zarinpal = array(
				'bankid' => 3 , 
				'active' => $zarinpal_active, 
				'username' =>$zarinpal_user ,
				'password' =>$zarinpal_pass ,
				'terminal_code' => $zarinpal_terminal , 
				'test_mode' =>$zarinpal_test 
			);
		
		if (
			checkHack::checkNum($data->time_back) &&
			checkHack::checkNum($data->show_pdf) &&
			checkHack::checkNum($data->captcha) &&
			checkHack::checkNum($data->show_email) &&
			checkHack::checkNum($data->bootstrap) &&
			checkHack::checkNum($saman_active) &&
			checkHack::checkNum($mellat_active) &&
			checkHack::checkNum($zarinpal_active) &&
			checkHack::checkNum($zarinpal_test) &&
			checkHack::checkString($saman_terminal) && 
			checkHack::checkString($mellat_user) && 
			checkHack::checkString($mellat_pass) && 
			checkHack::checkString($mellat_terminal)
		) {
			if($zarinpal_active != $zarinpal_test){
				$model = $this->getModel('settings');
				$model->updateMainSettings($data);
				foreach($port as  $bank){
					$model->updatePortSettings($bank);
				}

				$link = JRoute::_('index.php?option=com_tinypayment&view=Settings', false);
				$app->redirect($link, '<h2>تنظیمات با موفقیت ذخیره شد</h2>', $msgType='Message'); 	
			}
			else if($zarinpal_active == 1 && $zarinpal_test == 1) {
				$link = JRoute::_('index.php?option=com_tinypayment&view=Settings', false);
				$app->redirect($link, '<h2>درگاه تست و اصلی زرین پال نمی تواند در یک زمان فعال باشد.</h2>', $msgType='Error'); 	
			}
			else {
				$model = $this->getModel('settings');
				$model->updateMainSettings($data);
				foreach($port as  $bank){
					$model->updatePortSettings($bank);
				}
				$link = JRoute::_('index.php?option=com_tinypayment&view=Settings', false);
				$app->redirect($link, '<h2>تنظیمات با موفقیت ذخیره شد</h2>', $msgType='Message'); 
			}	
		}
		else {
			$link = JRoute::_('index.php?option=com_tinypayment&view=Settings', false);
			$app->redirect($link, '<h2>خطا در ذخیره تنظیمات . لطفا ورودی های خود را کنترل کنید</h2>', $msgType='Error'); 	
		}	

	  $app->close();	
	}
	
}
