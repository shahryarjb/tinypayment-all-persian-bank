<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
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
	}
}

