<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
// No direct access to this file
defined('_JEXEC') or die('Restricted access');
 
// Include dependancy of the main model form
jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
jimport('joomla.filesystem.file'); // for file upload // تست امنیت
/**
 * UpdHelloWorld Model
 */
require_once JPATH_SITE .'/components/com_tinypayment/helpers/jdf.php';
require_once JPATH_SITE .'/components/com_tinypayment/helpers/tinypayment.php';
require_once JPATH_SITE .'/components/com_tinypayment/helpers/mellat.php';
require_once JPATH_SITE .'/components/com_tinypayment/helpers/saman.php';
require_once JPATH_SITE .'/components/com_tinypayment/helpers/zarinpal.php';
require_once JPATH_SITE .'/components/com_tinypayment/helpers/otherport.php';
jimport('joomla.user.helper');

class TinyPaymentModelDashboard extends JModelForm
{
	/**
	 * @var object item
	 */
	protected $item;

	/**
	 * Get the data for a new qualification
	 */
	public function getForm($data = array(), $loadData = true)
	{
 
        $app = JFactory::getApplication('site');
        		// Get the form.
		$form = $this->loadForm('com_tinypayment.form', 'dashboard', array('control' => 'jform', 'load_data' => true));
		if (empty($form)) {
			return false;
		}
		return $form;
 
	}
	
	/**
	 * Method to get the script that have to be included on the form
	 *
	 * @return string	Script files
	 */
	public function getScript() 
	{
		return '/components/com_tinypayment/models/forms/form.js';
	}

	/**
	 * Get the message
	 * @return object The message to be displayed to the user
	 */
	function &getItem()
	{
		if (!isset($this->_item))
		{
			$cache = JFactory::getCache('com_tinypayment', '');
			$id = $this->getState('tinypayment.id');
			$this->_item =  $cache->get($id); // اینجا نیاز به امنیت نداره ؟
			if ($this->_item === false) {
 
			}
		}
		return $this->_item;
	}


	public function portName ($id) {
		switch(intval($id)) {
			case 0:$out = "درگاه انتخاب نشده است";break;
			case 1:$out = "ملت";break;
			case 2:$out = "ملی";break;
			case 3:$out = "زرین پال";break;
			case 4:$out = "پی لاین";break;
			case 5:$out = "جهان پی";break;
			case 6:$out = "پارسیان";break;
			case 7:$out = "پاسارگاد";break;
			case 8:$out = "صادرات";break;
			case 9:$out = "سامان";break;
			default:$out = "درگاه انتخاب نشده است";break;
		}
		return $out;	
	}
	
	
	public function getTinyMsg ($portId,$msgId) {
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('msg')
			->from($db->qn('#__tinypayment_msgs'));
		$query->where(
			$db->qn('port_id') . ' = ' . $db->q($portId) 
							. ' AND ' . 
			$db->qn('msg_id') . ' = ' . $db->q($msgId)
		);
		$db->setQuery((string)$query); 
		$result = $db->loadResult();
		return $result;
	}
	
	//==================================================================================== pdf
	//prepare data 
	public function preData ($id) {
			$user = JFactory::getUser();
			$paymentInfo = $this->getPayment(null,$id)[0];
			$newPaymentInfo[] = intval($id);
			$newPaymentInfo[] = preg_replace('/\s+/', '_', $paymentInfo->pay_title);
			$newPaymentInfo[] = preg_replace('/\s+/', '_', $paymentInfo->payer_name);
			$newPaymentInfo[] = jdate("o/m/j",TinyPaymentHelper::convert_date_to_unix($paymentInfo->create_time));
			$newPaymentInfo[] = round($paymentInfo->price,2);
			$newPaymentInfo[] = $user->username;
			$newPaymentInfo[] = $paymentInfo->payer_mobile;
			$newPaymentInfo[] = $paymentInfo->payer_email;
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo->pay_description);
			$newPaymentInfo[] = $paymentInfo->tracking_code;
			$newPaymentInfo[] = $this->portName($paymentInfo->port_id);
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo->admin_description);
			$newPaymentInfo[] = preg_replace('/\s+/', '_',TinyPaymentHelper::orderStatus($paymentInfo->order_status));
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo->result_message);

		return array($newPaymentInfo);
	}
	//------------------
	
	public function CallPdf($data,$stat) {
		$user = JFactory::getUser();
		$info = $this->preData($data);
		$this->pdf($info,$data);
		$filePath = JPATH_ROOT . '/media/com_tinypayment/images/pdf/invoice-'.$data.'-'.$user->id.'.pdf';
		if ($stat == null)
			$this->processDownload($filePath,'invoice-'.$data.'-'.$user->id.'.pdf',true);
	}
	/**
	 * Generate invoice PDF
	 * @param array $cid
	 */
	public static function pdf ($data,$id) {
		$mainframe = JFactory::getApplication();
		$sitename = $mainframe->getCfg("sitename");
		require_once JPATH_COMPONENT_SITE . "/tcpdf/tcpdf.php";

		$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

		// set document information
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor($sitename);
		$pdf->SetTitle('فاکتور');
		$pdf->SetSubject('فاکتور');
		$pdf->SetKeywords('فاکتور');

		// set default header data
		$pdf->SetHeaderData('tinypayment_invoice_logo.png', 30, JURI::root() , ' ', array(0,64,255), array(0,64,128));
		//$pdf->setFooterData(array(0,64,0), array(0,64,128));

		// set header and footer fonts
		$pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
		$pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

		// set default monospaced font
		$pdf->SetDefaultMonospacedFont(PDF_FONT_MONOSPACED);

		// set margins
		$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
		$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
		$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);

		// set auto page breaks
		$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

		// set image scale factor
		$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

		// set some language-dependent strings (optional)
		if (@file_exists(dirname(__FILE__).'/lang/eng.php')) {
		require_once(dirname(__FILE__).'/lang/eng.php');
		$pdf->setLanguageArray($l);
		}

		// ---------------------------------------------------------

		// set font
		$pdf->SetFont('freeserif', '', 12, '', true);

		// add a page
		$pdf->AddPage();

		$pdf->Write(0, 'فاکتور پرداخت', '', 0, 'L', true, 0, false, false, 0);
		$pdf->Ln();

		//-----------------------------------------------------------
		$out = '<table style="border-color: #000000;" border="1"> 
		<tbody> 
				<tr> <td>'.htmlspecialchars($data[0][0], ENT_COMPAT, 'UTF-8').'</td> <td> شماره سفارش </td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][1], ENT_COMPAT, 'UTF-8')).'</td> <td> عنوان سفارش </td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][2], ENT_COMPAT, 'UTF-8')).'</td> <td>  نام مشتری </td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][3], ENT_COMPAT, 'UTF-8')).'</td> <td> تاریخ صدور فاکتور </td></tr> 
				<tr> <td>'.htmlspecialchars($data[0][4], ENT_COMPAT, 'UTF-8').'</td> <td> مبلغ فاکتور </td></tr> 
				<tr> <td>'.htmlspecialchars($data[0][5], ENT_COMPAT, 'UTF-8').'</td> <td>نام کاربری</td></tr> 
				<tr> <td>'.htmlspecialchars($data[0][6], ENT_COMPAT, 'UTF-8').'</td> <td>ایمیل  </td></tr> 
				<tr> <td>'.htmlspecialchars($data[0][7], ENT_COMPAT, 'UTF-8').'</td> <td>موبایل</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][8], ENT_COMPAT, 'UTF-8')).'</td> <td>توضیحات مشتری</td></tr> 
				<tr> <td>'.htmlspecialchars($data[0][9], ENT_COMPAT, 'UTF-8').'</td> <td>شماره پیگری</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][10], ENT_COMPAT, 'UTF-8')).'</td> <td> بانک پرداخت کننده</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][11], ENT_COMPAT, 'UTF-8')).'</td> <td> توضیحات مدیر</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][12], ENT_COMPAT, 'UTF-8')).'</td> <td>وضعیت سفارش </td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][13], ENT_COMPAT, 'UTF-8')).'</td> <td>وضعیت پرداخت </td></tr> 
		</tbody> </table>';
		$out .= '<p style="text-align: right;"> </p>
			<p dir="rtl" style="text-align: right;">کامپوننت آسان پرداخت <a href="https://trangell.com/fa/">ترانگل</a></p>';
		$pdf->writeHTML($out, true, false, false, false, '');
		//-----------------------------------------------------------

		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$user = JFactory::getUser();
		$filePath = JPATH_ROOT . '/media/com_tinypayment/images/pdf/invoice-'.$id.'-'.$user->id.'.pdf';
		$pdf->Output($filePath, 'F');
		
	}
	
	public static function processDownload($filePath, $filename, $download = false)
	{
		jimport('joomla.filesystem.file') ;						
		$fsize = @filesize($filePath);
		$mod_date = date('r', filemtime($filePath) );		
		if ($download) {
		    $cont_dis ='attachment';   
		} else {
		    $cont_dis ='inline';
		}		
		$ext = JFile::getExt($filename) ;
		$mime = 'application/pdf';
		// required for IE, otherwise Content-disposition is ignored
		if(ini_get('zlib.output_compression'))  {
			ini_set('zlib.output_compression', 'Off');
		}
	    header("Pragma: public");
	    header("Cache-Control: must-revalidate, post-check=0, pre-check=0");
	    header("Expires: 0");		
	    header("Content-Transfer-Encoding: binary");
		header('Content-Disposition:' . $cont_dis .';'
			. ' filename="' . JFile::getName($filename) . '";' 
			. ' modification-date="' . $mod_date . '";'
			. ' size=' . $fsize .';'
			); //RFC2183
	    header("Content-Type: "    . $mime );			// MIME type
	    header("Content-Length: "  . $fsize);
	
	    if( ! ini_get('safe_mode') ) { // set_time_limit doesn't work in safe mode
		    @set_time_limit(0);
	    }
	    self::readfile_chunked($filePath);
	}
	
	/**
	 * 
	 * Function to read file
	 * @param string $filename
	 * @param boolean $retbytes
	 * @return boolean|number
	 */
	public static function readfile_chunked($filename, $retbytes = true)
	{
		$chunksize = 1 * (1024 * 1024); // how many bytes per chunk
		$buffer = '';
		$cnt = 0;
		$handle = fopen($filename, 'rb');
		if ($handle === false)
		{
			return false;
		}
		while (!feof($handle))
		{
			$buffer = fread($handle, $chunksize);
			echo $buffer;
			@ob_flush();
			flush();
			if ($retbytes)
			{
				$cnt += strlen($buffer);
			}
		}
		$status = fclose($handle);
		if ($retbytes && $status)
		{
			return $cnt; // return num. bytes delivered like readfile() does.
		}
		return $status;
	}

	public function getPayment($id) {
		$db = JFactory::getDbo();
		$user = JFactory::getUser();
		$query = $db->getQuery(true);
		$query->select('tp.id,tp.pay_title,tp.pay_description,tp.payer_name,tp.payer_mobile,tp.payer_email,tp.order_status,tp.admin_description,tp.create_time,
						tt.port_id,tt.price,tt.tracking_code,tt.last_change_date,
						tsl.result_message')
			->from($db->qn('#__tinypayment_paymentinfo') . ' as tp')
			->leftJoin($db->qn('#__tinypayment_transactions') . ' as tt ON '.$db->qn('tt.payment_id').' = '.$db->qn('tp.id'))
			->leftJoin($db->qn('#__tinypayment_status_log') . ' as tsl ON '.$db->qn('tsl.transaction_id'). ' = ' . $db->qn('tt.id'));			
		if (isset($id)){
			$query->where($db->qn('tp.id') . ' = ' . $db->q($id));
			$query->where($db->qn('tp.payer_email') . ' LIKE ' . $db->q($user->email));
		}
		else {
			$query->where($db->qn('tp.payer_email') . ' LIKE ' . $db->q($user->email));
			//-----------------------------------
			$jinput = JFactory::getApplication()->input;
			$mainframe = JFactory::getApplication();
			$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
			$limitstart =  $jinput->get->get('limitstart', '0', 'INT'); 	
			$query->setLimit($limit,$limitstart);
		}
	
		$db->setQuery((string)$query); 
		$result = $db->loadObjectlist();
		return $result;
	}

	public function getTotal() { 
		$user = JFactory::getUser(); 
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*');
		$query->from($db->qn('#__tinypayment_paymentinfo') . ' as tp');
		$query->where($db->qn('tp.payer_email') . ' LIKE  ' . $db->q($user->email));
		$db->setQuery((string)$query); 
		$result = $db->loadObjectlist();
		return $result;
	}

	public function sendEmail(){
		$user = JFactory::getUser();
		$jinput = JFactory::getApplication()->input;
		$input = $jinput->getArray(array(
			'jform' => array(
				'id' => 'INT',
				'stat' => 'INT'
			)
		));
	
		$id = $input['jform']['id'];
		$stat = $input['jform']['stat'];
		// make pdf
		$this->CallPdf($id,$stat);
		TinyPaymentHelper::sendEmail($id,$user);
	}

}
