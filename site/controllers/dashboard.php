<?php 
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access.
defined('_JEXEC') or die;


class TinyPaymentControllerDashboard extends JControllerForm
{

	public function getModel($name = '', $prefix = '', $config = array('ignore_request' => true))
	{
		return parent::getModel($name, $prefix, array('ignore_request' => false));
	}
 

	public function pdf2() {
		JSession::checkToken( 'post' ) or die( 'Invalid Token' );
		$jinput = JFactory::getApplication()->input;
		$data = $jinput->get('id', '', 'string');
		$app	= JFactory::getApplication();
		$app-> setHeader('Content-Type', 'application/pdf; charset=utf-8', true);
		$model	= $this->getModel('dashboard');
		$model->CallPdf($data,null);
		$app->close();	
	}

	public function sendEmail(){
		JSession::checkToken( 'request' ) or die( 'Invalid Token' ); // for javascript
		$app 	= JFactory::getApplication();
		$model 	= $this->getModel('dashboard');
		$email = $model->sendEmail();
		$app->close();
	}
}
