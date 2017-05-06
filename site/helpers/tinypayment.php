<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
class TinyPaymentHelper
{	
	public static function convert_date_to_unix($date_time) {
		$user = JFactory::getUser();
		$timeZone = $user->getParam('timezone', 'UTC');
		$myDate = JDate::getInstance($date_time, $timeZone); 
		return $myDate->toUnix();
	}

	public static function orderStatus($id) {
		switch($id) {
			case 1:$out = "کنسل شده";break;
			case 2:$out = "کنسل نشده";break;
			case 3:$out = "برگشت مبلغ";break;
			case 4:$out = "انجام شده";break;
			case 5:$out = "رد کردن";break;
			case 6:$out = "منقضی شده";break;
			case 7:$out = "ناموفق";break;
			case 8:$out = "معلق";break;
			case 9:$out = "بررسی شده";break;
			case 10:$out = "در حال بررسی";break;
			case 11:$out = "پرداخت مجدد";break;
			case 12:$out = "برگشت";break;
			case 13:$out = "پست شده";break;
			case 14:$out = "باطل شده";break;
			default:$out ="بررسی نشده";
		}
		return $out;	
	}

	public static function portName ($id) {
		switch($id) {
			case 0:$out = "بدون درگاه";break;
			case 1:$out = "ملت";break;
			case 2:$out = "ملی";break;
			case 3:$out = "زرین پال";break;
			case 4:$out = "پی لاین";break;
			case 5:$out = "جهان پی";break;
			case 6:$out = "پارسیان";break;
			case 7:$out = "پاسارگاد";break;
			case 8:$out = "صادرات";break;
			case 9:$out = "سامان";break;
			default:$out= "بدون درگاه";break;
		}
		return $out;	
	}

	public static function cRight() {
		$cright = '<div class="margin"></div>';
		$cright .= '<div class="span12">';
		$cright .= '<div style="text-align: center">';
		$cright .=  'برنامه نویسی  <a title="کامپوننت آسان پرداخت جوملا" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا" rel="alternate">کامپوننت آسان پرداخت جوملا</a>  :  <a title="گروه برنامه نویسی ترانگل" href="https://trangell.com/fa/" rel="alternate">ترانگل</a>';
		$cright .= '</div>';
		$cright .= '</div>';
		$cright .= '<div class="clearfix"></div>';
		$cright .= '<div class="margin"></div>';
		echo $cright;
		JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/customadmin.css');
		JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/custom.css');
//---------------------------------------- config 
		require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php'; 
		$mconfig = new config();
		$loadMainConfig = $mconfig->loadMainSettings();
		if ($loadMainConfig->bootstrap == 1) {
			JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/bootstrap/3.3.7/css/bootstrap.min.css');
		}
//----------------------------------------	
	}

	public static function pagination($total){
		$app = JFactory::getApplication()->input; 
		$mainframe = JFactory::getApplication();
		$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $app->post->get('limitstart', 0, '', 'INT'); 
		$pagination = new JPagination($total, $limitstart, $limit);
		return $pagination;
	}

	public static function sendEmail($id,$user){
		//Fetch the Mail Object
		$mailer = JFactory::getMailer();
		//====================================
		//Set a Sender
		$config = JFactory::getConfig();
		$sender = array( 
			$config->get( 'mailfrom' ),
			$config->get( 'fromname' ) 
		);
		$mailer->setSender($sender);
		//====================================
		//Recipient
		$recipient = $user->email;
		$mailer->addRecipient($recipient);
		//====================================
		//Create the Mail
		$mailer->setSubject('فاکتور شما');
		$body   = '<h2>فاکتور خرید از سایت ترانگل</h2>';
		$mailer->isHtml(true);
		$mailer->Encoding = 'base64';
		$mailer->setBody($body);
		// Optional file attached
		$mailer->addAttachment(JPATH_ROOT . '/media/com_tinypayment/images/pdf/invoice-'.$id.'-'.$user->id.'.pdf');
		//====================================
		//Sending the Mail
		$send = $mailer->Send();
		return $send;
	}
}

