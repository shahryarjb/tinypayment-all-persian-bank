<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

abstract class TinyPaymentHelper
{
	public static function menuAdmin() {
		$menu = ' <ul class="sidbarsICo">
		 <li class="lisidbars">
		<a href="index.php?option=com_tinypayment&view=storepayments">
		<i class="fa fa-home" aria-hidden="true"></i>
		ذخیره پرداخت ها
		</a>
		</li>
		<li class="lisidbars">
		<a href="index.php?option=com_tinypayment&view=outputs">
		<i class="fa fa-file-text-o" aria-hidden="true"></i>
		دریافت خروجی
		</a>
		</li>
		<li class="lisidbars">
		<a href="index.php?option=com_tinypayment&view=statistics">
		<i class="fa fa-balance-scale" aria-hidden="true"></i>
		آمار
		</a>
		</li>
		<li class="lisidbars">
		<a href="index.php?option=com_tinypayment&view=guide">
		 <i class="fa fa-diamond" aria-hidden="true"></i>
		راهنما
		</a>
		</li>
		<div class="clearfix"></div>
		</ul>';
		echo $menu;
	}

	public static function cRight() {
		echo '<div class="span6 cright">
            	تمامی حقوق " <a title="کامپوننت آسان پرداخت جوملا" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا" rel="alternate">کامپوننت آسان پرداخت جوملا</a> " متعلق به وبسایت " <a title="گروه برنامه نویسی ترانگل" href="https://trangell.com/fa/" rel="alternate">ترانگل</a> " می باشد. 
          		<code>
            	نسخه 1.0.0
          		</code>
        		</div>';
	}
	
	public static function getActions($messageId = 0)
	{	
		$result	= new JObject;

		if (empty($messageId)) {
			$assetName = 'com_tinypayment';
		}
		else {
			$assetName = 'com_tinypayment.message.'.(int) $messageId;
		}

		$actions = JAccess::getActions('com_tinypayment', 'component');

		foreach ($actions as $action) {
			$result->set($action->name, JFactory::getUser()->authorise($action->name, $assetName));
		}

		return $result;
	}
	
	public static function convert_date_to_unix($date_time) {
		$user = JFactory::getUser();
		$timeZone = $user->getParam('timezone', 'UTC');
		$myDate = JDate::getInstance($date_time, $timeZone); 
		return $myDate->toUnix();
	}

	public static function imgAdmin() {
	          $imgreturn = "";
	          $imgreturn .= '<a href="index.php?option=com_tinypayment&view=storepayments">';
                      $imgreturn .= '<img src="';
                      $imgreturn .= JURI::root();
                      $imgreturn .= 'components/com_tinypayment/ui/dist/img/admin.jpg"';
                      $imgreturn .= ' class="img-circle admin-students-circle" alt="User Image" style="box-shadow: 1px 1px 1px #dadada;"></a>';
                      echo $imgreturn;
	}
}
