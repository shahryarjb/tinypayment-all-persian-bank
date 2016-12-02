<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

class TinyPaymentControllerStorePayment extends JControllerForm
{

	protected function allowAdd($data = array()) {
		return parent::allowAdd($data);
	}
	protected function allowEdit($data = array(), $key = 'id')
	{
		$id = isset( $data[ $key ] ) ? $data[ $key ] : 0;
		if( !empty( $id ) )
		{
			return JFactory::getUser()->authorise( "core.edit", "com_tinypayment.message." . $id );
		}
	}

	public function csv2() {
		JSession::checkToken('post') or die( 'Invalid Token' );
		$app = JFactory::getApplication('site')->input;
		$id = $app->get('id');
		$model = $this->getModel('storepayment');
		$row = $model->csv($id);
		$title = array(array('نام_پرداخت_کننده','آی_پی_پرداخت_کننده','شماره_همراه','ایمیل_پرداخت_کننده','عنوان_پرداخت','توضیحات','تاریخ_پرداخت','شماره_کارت','درگاه_پرداخت','مبلغ','وضعیت_پرداخت','شناسه_پرداخت'));
		$data  = array_merge($title,$row);
		foreach ($data as  $str) {
				print implode(';', $str)."\n"; 	
		}
		$pdfName = uniqid();
		$app = JFactory::getApplication();
		$app-> setHeader('Content-Type', 'application/cvs; charset=utf-8', true)
		-> setHeader('Content-Disposition', 'attachment; filename="'.'invoice-'.$pdfName.'.csv"', true)
		-> setHeader('Content-Transfer-Encoding', 'binary', true)
		-> setHeader('Expires', '0', true)
		-> setHeader('Pragma','no-cache',true);

		$app->sendHeaders();
		$app->close();	
	}
	
	public function pdf2() {
		JSession::checkToken('post') or die( 'Invalid Token' );
		$app = JFactory::getApplication();
		$app-> setHeader('Content-Type', 'application/pdf; charset=utf-8', true);
		$model = $this->getModel('storepayment');
		$model->CallPdf();
		$app->close();	
	}
}
