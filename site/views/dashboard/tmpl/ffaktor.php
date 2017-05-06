<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE  .'/components/com_tinypayment/helpers/tinypayment.php';
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php';
$model = $this->getModel('dashboard');
$app     = JFactory::getApplication();
$user = JFactory::getUser();
$jinput = JFactory::getApplication()->input;
$id = $jinput->get->get('id', '0', 'INT');
if ($user->guest) {
	$link = JRoute::_('index.php?option=com_tinypayment&view=other&layout=other',false);
	$app->redirect($link); 
}

//------------------------- config 
$mconfig = new config();
$loadMainConfig = $mconfig->loadMainSettings();
//------------------------- 
?>
<div class="row-fluid">
	<div class="span12">
		<div class="row-fluid">
			
		<div class="span3 mainsid">
		<?php 
		if ($model->getPayment($id) != null){
				$data =  $model->getPayment($id)[0];
		 ?>
		<div class="span12 logo-admin">
           <a href=""><img src="components/com_tinypayment/ui/dist/img/admin.jpg" class="img-circle admin-students-circle" alt="User Image" style="box-shadow: 1px 1px 1px #dadada;"></a>           
              <div class="clearfix"></div>
              <div class="row-fluid margin"></div>
              <div class="text-back-top">
			 		<?php echo htmlspecialchars($data->payer_name, ENT_COMPAT, 'UTF-8'); ?>            
			 	</div>
			      </div>
			      <div class="clearfix"></div>
			     <br>
			<?php
				echo '<span>شماره سفارش:</span> '.intval($data->id).'<br/>';
				echo '<span> نام مشتری:</span>  '.htmlspecialchars($data->payer_name, ENT_COMPAT, 'UTF-8').'<br/>';
				echo 'تاریخ صدور فاکتور:</span>  '.htmlspecialchars(jdate("o/m/j",TinyPaymentHelper::convert_date_to_unix($data->create_time)), ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> مبلغ فاکتور:</span>  '.round($data->price,0).'<br/>';
				echo '<span> ایمیل:</span>  '.htmlspecialchars($data->payer_email, ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> موبایل:</span>  '.htmlspecialchars($data->payer_mobile, ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> شماره پیگری:‌</span> '.htmlspecialchars($data->tracking_code, ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> بانک پرداخت کننده:</span>  '.htmlspecialchars(TinyPaymentHelper::portName($data->port_id), ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> وضعیت سفارش:‌</span> '.htmlspecialchars(TinyPaymentHelper::orderStatus($data->order_status), ENT_COMPAT, 'UTF-8').'<br/>';
				echo '<span> وضعیت پرداخت :‌</span> '.htmlspecialchars($data->result_message, ENT_COMPAT, 'UTF-8').'<br/>';
			?>
			<div class="margin"></div>

			<form class="form-validate" action=""  method="post" id="userinfo" name="form">
			<?php if ($loadMainConfig->show_pdf == 1) { ?>
			<button type="submit" class="btn btn-small"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> خروجی pdf</button>
			<?php } ?>

			<?php if ($loadMainConfig->show_email == 1) { ?>
			<a href="<?php echo JRoute::_('index.php?option=com_tinypayment&view=dashboard'); ?>" class="btn btn-small">
			<i class="fa fa-credit-card" aria-hidden="true"></i>
			 فاکتور ها</a>
			 <?php } ?>
			<input type="hidden" name="task" value="dashboard.pdf2" />
			<?php echo JHtml::_('form.token'); ?>
			</form>
			<!-- <input class="btn btn-small" name="add" type="button" value="email" onClick="sendFaktor()">  -->
			<button onclick="sendFaktor()" name="add" class="btn btn-small">
			<i class="fa fa-envelope-o" aria-hidden="true"></i>
			ارسال ایمیل
			</button>
			<div class="margin"></div>
		</div> <!-- </right-side or left side> -->
		<div class="span9 intab">
		<h3>
			<?php 
				echo '<i class="fa fa-info"></i><span> عنوان سفارش :</span>  '.htmlspecialchars($data->pay_title, ENT_COMPAT, 'UTF-8').'<br/><hr>';

			 ?>
		</h3>
		<p class="bg-success bgalerts">
			<?php 
			echo '<i class="fa fa-info"></i><span> توضیحات مشتری:‌</span> <br><br>'.htmlspecialchars($data->pay_description, ENT_COMPAT, 'UTF-8').'<br/>';
			 ?>
		</p>
		<p class="bg-info bgalerts">
			<?php 
				echo '<i class="fa fa-info"></i><span> توضیحات مدیر:</span>  <br><br>'.htmlspecialchars($data->admin_description, ENT_COMPAT, 'UTF-8').'<br/>';
			 ?>
		</p>

		</div> <!-- content main -->
		</div>

	</div>
</div>
<div class="clearfix"></div>
				<!-- inja comments -->
<div class="clearfix"></div>
<?php 
}
else {
	$msg= 'هیچ فاکتوری وجود ندارد';
	$link = JRoute::_('index.php?option=com_tinypayment&view=dashboard',false);
	$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
}	
?>
<div class="clearfix"> </div>
<?php TinyPaymentHelper::cRight(); ?>

<script>
	JQ = jQuery;
jQuery.noConflict();
function sendFaktor(){
	JQ.ajax({
		type: 'POST',
		url: 'index.php?option=com_tinypayment&view=dashboard',
		data: {
			task: 'dashboard.sendEmail',
			id: <?php echo $id; ?>,
			stat: 1,
			'<?php echo JSession::getFormToken(); ?>': 1
		},
		success: function(data) {
			// JQ("#sectionName").html(data);
			alert('send email ok');
		},
		error: function(data) {
			alert('error');
		}
	}); 
}
</script>
