<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/jdf.php';

class TinyPaymentModelStorePayment extends JModelAdmin {

	public function getTable($type = 'storepayment', $prefix = 'TinyPaymentTable', $config = array()) {
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true) {

		$form = $this->loadForm(
			'com_tinypayment.storepayment',
			'storepayment',
			array(
				'control' => 'jform',
				'load_data' => $loadData
			)
		);

		if (empty($form))
		{
			return false;
		}

		return $form;
	}

	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(
			'com_tinypayment.edit.storepayment.data',
			array()
		);

		if (empty($data))
		{
			$data = $this->getItem();
		}

		return $data;
	}
	//========================================= store edit_time
	protected function prepareTable($table) 
	{
		$date = JFactory::getDate();
		$table->edit_time = $this::convert_date_to_unix($date);
	}

	protected function canDelete($record)
	{
		if( !empty( $record->id ) )
		{
			return JFactory::getUser()->authorise( "core.delete", "com_tinypayment.message." . $record->id );
		}
	}
	
	public function getPaymentInfo ($id) {
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('pt.port_id,pt.price,pt.tracking_code as id_transaction,pt.cardNumber,
						psl.result_message,psl.log_date,
						p.payer_ip,p.payer_name,p.payer_mobile,p.payer_email,p.pay_title,p.pay_description,p.create_time,p.edit_time')
			->from($db->qn('#__tinypayment_transactions') . ' as pt')
			->leftJoin($db->qn('#__tinypayment_status_log') . ' as psl ON psl.transaction_id = pt.id')
			->leftJoin($db->qn('#__tinypayment_paymentinfo') . ' as p ON p.id = pt.payment_id')
			->where($db->qn('payment_id') . ' = ' . $db->q($id));
		$db->setQuery((string)$query); 
		$res = $db->loadAssoc();
		return $res;
	}
	
	public function portName ($id) {
		switch(intval($id)) {
			case 1:$out = "ملت";break;
			case 2:$out = "ملی";break;
			case 3:$out = "زرین پال";break;
			case 4:$out = "پی لاین";break;
			case 5:$out = "جهان پی";break;
			case 6:$out = "پارسیان";break;
			case 7:$out = "پاسارگاد";break;
			case 8:$out = "صادرات";break;
			case 9:$out = "سامان";break;
			default:$out = "بدون درگاه";break;
		}
		return $out;	
	}
	
	public function convert_date_to_unix($date_time) {
    		// Get the User and their timezone
		    	$user = JFactory::getUser();
		    	$timeZone = $user->getParam('timezone', 'UTC');

	    	// Create JDate object set to now in the users timezone.
	    	    $myDate = JDate::getInstance($date_time, $timeZone);

	    		return $myDate->toUnix();
	}

	public function csv ($id) {
			$paymentInfo = $this->getPaymentInfo($id);	
			$newPaymentInfo[] = preg_replace('/\s+/', '_', $paymentInfo['payer_name']);
			$newPaymentInfo[] = $paymentInfo['payer_ip'];
			$newPaymentInfo[] = $paymentInfo['payer_mobile'];
			$newPaymentInfo[] = $paymentInfo['payer_email'];
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo['pay_title']);
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo['pay_description']);
			$newPaymentInfo[] = jdate("o/m/j",$this->convert_date_to_unix($paymentInfo['log_date']));
			$newPaymentInfo[] = $paymentInfo['cardNumber'];
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$this->portName($paymentInfo['port_id']));
			$newPaymentInfo[] = round($paymentInfo['price'],2);
			$newPaymentInfo[] = preg_replace('/\s+/', '_',$paymentInfo['result_message']);
			$newPaymentInfo[] = $paymentInfo['id_transaction'];
		return array($newPaymentInfo);
	}
	
	public function CallPdf() {
		$app = JFactory::getApplication('site');
		$id = $app->input->getInt('id'); 
		$info = $this->csv($id);
		$pdfName = uniqid();
		$this->pdf($info,$pdfName);
		$filePath = JPATH_ROOT . '/media/com_tinypayment/images/pdf/invoice-'. $pdfName .'.pdf';
		$this->processDownload($filePath,'invoice-'. $pdfName .'.pdf',true);
	}
	/**
	 * Generate invoice PDF
	 * @param array $cid
	 */
	public static function pdf ($data,$pdfName) {
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
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][0], ENT_COMPAT, 'UTF-8')).'</td> <td>نام پرداخت کننده</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][1], ENT_COMPAT, 'UTF-8')).'</td> <td>آدرس آی پی پرداخت کننده</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][2], ENT_COMPAT, 'UTF-8')).'</td> <td>شماره همراه پرداخت کننده</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][3], ENT_COMPAT, 'UTF-8')).'</td> <td>ایمیل پرداخت کننده</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][4], ENT_COMPAT, 'UTF-8')).'</td> <td>عنوان پرداخت</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][5], ENT_COMPAT, 'UTF-8')).'</td> <td>توضیحات</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][6], ENT_COMPAT, 'UTF-8')).'</td> <td>تاریخ پرداخت</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][7], ENT_COMPAT, 'UTF-8')).'</td> <td>شماره کارت</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][8], ENT_COMPAT, 'UTF-8')).'</td> <td>درگاه</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][9], ENT_COMPAT, 'UTF-8')).'</td> <td>مبلغ</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][10], ENT_COMPAT, 'UTF-8')).'</td> <td>وضعیت پرداخت</td></tr> 
				<tr> <td>'.str_replace("_"," ",htmlspecialchars($data[0][11], ENT_COMPAT, 'UTF-8')).'</td> <td>شناسه پرداخت</td></tr> 
				
		</tbody> </table>';
		$out .= '<p style="text-align: right;"> </p>
<p dir="rtl" style="text-align: right;">کامپوننت آسان پرداخت <a href="https://trangell.com/fa/">ترانگل</a></p>';
		
		$pdf->writeHTML($out, true, false, false, false, '');
		//-----------------------------------------------------------
		
		// ---------------------------------------------------------
		$app = JFactory::getApplication('site');
		$id = $app->input->getInt('id'); 
		// Close and output PDF document
		// This method has several options, check the source code documentation for more information.
		$filePath = JPATH_ROOT . '/media/com_tinypayment/images/pdf/invoice-'. $pdfName .'.pdf';
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
}
