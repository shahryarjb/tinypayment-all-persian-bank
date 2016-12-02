<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'otherport.php'; 
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'inputcheck.php';
if (!JSession::checkToken( 'post' )) {
	$newData = checkHack::joinStrip('tokenInvalid');
	other::reqForm($newData,'token');
}
JHtml::_('behavior.formvalidator'); 
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/customadmin.css');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/custom.css');
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
$file = 'https://www.google.com/recaptcha/api.js?onload=onloadCallback&render=explicit&hl=' . JFactory::getLanguage()->getTag();
JHtml::_('script', $file);
$model = $this->getModel('form');
$app	= JFactory::getApplication(); 
$jinput = JFactory::getApplication()->input;
$input = $jinput->getArray(array(
	'jform' => array(
		'payer_name' => 'STRING',
		'payer_mobile' => 'STRING',
		'payer_email' => 'STRING',
		'pay_title' => 'STRING',
		'pay_description' => 'STRING',
		'pay_price' => 'INT'
		)
	));
$name = $input['jform']['payer_name'];
$mobile = $input['jform']['payer_mobile'];
$email = $input['jform']['payer_email'];
$title = $input['jform']['pay_title'];
$description = $input['jform']['pay_description'];
$price = $input['jform']['pay_price'];
$port = $jinput->post->get('pay_port', '0', 'INT');
$nPort = $model->portName($port);
$remoteip  = other::getRealIpAddr();
$payerIp = $remoteip;
$createTime = time();

if (
	checkHack::checkString($title) && 
	checkHack::checkAlphaNumberic($description) &&  
	checkHack::checkString($name) && 
	checkHack::checkMobile($mobile) &&  
	checkHack::checkEmail($email) &&  
	checkHack::checkString($payerIp) && 
	checkHack::checkNum($createTime) && 
	checkHack::checkNum($price) &&  
	checkHack::checkNum($port) 	
	){
?>
<form class="form-validate" action=""  method="post" id="userinfo" name="form">


<div class="callout callout-info">
کاربر محترم پیش فاکتور هیچ تضمین قانونی ندارد فقط برای اطلاع رسانی نمایش گزاشته می شود
</div>
<h3>پیش فاکتور کاربر <? echo htmlspecialchars($name, ENT_COMPAT, 'UTF-8'); ?></h3>
<div class="row-fluid">
				<div class="span12">
				<div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                              <tbody>
								<tr>
								<td>نام و نام خانوادگی</td>
								<td><? echo htmlspecialchars($name, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>شماره موبایل</td>
								<td><? echo htmlspecialchars($mobile, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>ایمیل</td>
								<td><? echo htmlspecialchars($email, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>موضوع</td>
								<td><? echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8'); ?> </td>
								</tr>
								<tr>
								<td>توضیحات</td>
								<td> <? echo htmlspecialchars($description, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>درگاه</td>
								<td><? echo htmlspecialchars($nPort, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>مبلغ</td>
								<td><? echo htmlspecialchars($price, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
                            </tbody>
                            </table>
  </div>
</div> 
				<div style="display: none;">
					<input id="payer_name" name="payer_name" type="text" class="required validate-username" value="<? echo htmlspecialchars($name, ENT_COMPAT, 'UTF-8'); ?>" readonly><br>
					<input name="payer_mobile" type="text" class="required validate-numeric" value="<? echo htmlspecialchars($mobile, ENT_COMPAT, 'UTF-8'); ?>" readonly><br>
					<input name="payer_email" type="text" class="required validate-email" value="<? echo htmlspecialchars($email, ENT_COMPAT, 'UTF-8'); ?>" readonly><br>
					<input name="pay_title" type="text" class="required validate-username" value="<? echo htmlspecialchars($title, ENT_COMPAT, 'UTF-8'); ?> " readonly><br>
					<input id="pay_description" name="pay_description" type="text" class="required validate-username" value="<? echo htmlspecialchars($description, ENT_COMPAT, 'UTF-8'); ?>" readonly><br>
					<select name="pay_port" type="text" class="required validate-numeric" readonly><option value="<? echo htmlspecialchars($port, ENT_COMPAT, 'UTF-8'); ?>" class="required"><? echo htmlspecialchars($nPort, ENT_COMPAT, 'UTF-8'); ?></option></select><br>
					<input id="pay_price" name="pay_price" type="text" class="required validate-numeric" value="<? echo htmlspecialchars($price, ENT_COMPAT, 'UTF-8'); ?>" readonly>
					<input id="payer_ip" name="payer_ip" type="text" class="required validate-username" value="<? echo htmlspecialchars($payerIp, ENT_COMPAT, 'UTF-8'); ?>" readonly>
					<input type="hidden" name="task" value="form.submit" />
				</div>
				<?php if ($app->getParams()->get('recapstatus') != null && $app->getParams()->get('recapstatus') == 1) {?>
					<div id="html_element" style="clear: both;"></div><br>
				<?php }?>
				<input id="pay" type="submit" value="پرداخت" class="validate btn btn-small btn-success"><br>
				<?php echo JHtml::_('form.token'); ?>

</form>
<script type="text/javascript">
<?php if ($app->getParams()->get('recapstatus') != null && $app->getParams()->get('recapstatus') == 1) {?>
	jQuery("#pay").hide(); 
<?php }?>
var onloadCallback = function() {
grecaptcha.render('html_element', {
  'sitekey' : '<?php echo $app->getParams()->get('pubrecapcod'); ?>',
  'callback' : test
});
};

function test() {
	jQuery("#pay").show(); 
  }

</script>
<?php } 
	else {
		$data = array($title,$description,$name,$mobile,$email,$payerIp,$createTime,$price,$port);
		$newData = checkHack::joinStrip($data);
		other::reqForm($newData,'hck2');
	}
?>
<?php TinyPaymentHelper::cRight(); ?>
