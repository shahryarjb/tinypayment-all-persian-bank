<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/jdf.php';

class TinyPaymentControllerOutputs extends JControllerForm {

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
		$url = JRoute::_('index.php?option=com_tinypayment&view=outputs', false);
		$app->redirect($url);
	}
	
	public function csv2() {
		// Check for request forgeries.
		JSession::checkToken() or die( 'Invalid Token' );
		$id = 1;
		$model = $this->getModel('outputs');
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->get('jform', '', 'string');
		$out =  $model->getPaymentInfo($post,'csv'); 
		//-----------------------
		$start = jdate("o_m_j",$out[1]['start'],"","","en");
		$end = jdate("o_m_j",$out[1]['end'],"","","en");
		//-----------------------
		$app = JFactory::getApplication();

		if ($out[0] != false){
			$out1 = array_chunk($out[0][0],12); // tabdile be chand arraye
	
			$title = array(array('نام_پرداخت_کننده','آی_پی_پرداخت_کننده','شماره_همراه','ایمیل_پرداخت_کننده','عنوان_پرداخت','توضیحات','تاریخ_پرداخت','شماره_کارت','درگاه_پرداخت','مبلغ','وضعیت_پرداخت','شناسه_پرداخت'));
			$data  = array_merge($title,$out1);
		
			foreach ($data as  $str) {
					print implode(';', $str)."\n"; 	
			}
			
			$app-> setHeader('Content-Type', 'application/cvs; charset=utf-8', true)
				-> setHeader('Content-Disposition', 'attachment; filename="'.'invoice-'.$start.'-'.$end.'.csv"', true)
				-> setHeader('Content-Transfer-Encoding', 'binary', true)
				-> setHeader('Expires', '0', true)
				-> setHeader('Pragma','no-cache',true);

			// Close the application gracefully.
			$app->sendHeaders();
			$app->close();	
			
			$date['start'] = $model->convert_date_to_unix($post['jform']['start']);
			$date['end'] = $model->convert_date_to_unix($post['jform']['end']);
			$model->storeLog($date); // insert log 
		}
		else {
			$link = JRoute::_('index.php?option=com_tinypayment&view=outputs',false);
			$app->redirect($link, '<h2>هیچ رکوردی در این تاریخ وجود ندارد.</h2>', $msgType='Error'); 
			$app->close();	
		}
	}
	
	public function pdf2() {
		// Check for request forgeries.
		JSession::checkToken() or die( 'Invalid Token' );
		$model = $this->getModel('outputs');
		$jinput = JFactory::getApplication()->input;
		$post = $jinput->get('jform', '', 'string');
		$app = JFactory::getApplication();
		$model = $this->getModel('outputs');
		$app-> setHeader('Content-Type', 'application/pdf; charset=utf-8', true);
		$out = $model->getPaymentInfo($post,'pdf'); 
		
		if ($out == false){
			$link = JRoute::_('index.php?option=com_tinypayment&view=outputs',false);
			$app->redirect($link, '<h2>هیچ رکوردی در این تاریخ وجود ندارد.</h2>', $msgType='Error'); 
			$app->close();	
		}
		else {
			$date['start'] = $model->convert_date_to_unix($post['jform']['start']);
			$date['end'] = $model->convert_date_to_unix($post['jform']['end']);
			$model->storeLog($date); // insert log 
		}
		
		$app->close();	
	}
}
