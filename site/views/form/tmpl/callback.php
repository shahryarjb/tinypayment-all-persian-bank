<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
$app	= JFactory::getApplication();
require_once JPATH_SITE .'/components/com_tinypayment/helpers/jdf.php';
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'otherport.php'; 
require_once JPATH_SITE . DS .'components'.DS.'com_tinypayment'.DS.'helpers'.DS.'inputcheck.php';
JHtml::_('behavior.formvalidator'); 
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/customadmin.css');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/custom.css');
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

$model = $this->getModel('form');
$session = JFactory::getSession();
$cryptUID = $session->get('uniqId');
if ($cryptUID != null && $cryptUID != '') {
	$paymentInfo = $model->getPaymentInfo($cryptUID)[0];
}
else {
	$paymentInfo ='';
}
?>
<form class="form-validate" action=""  method="post" id="userinfo" name="form">
				<?php if ($paymentInfo != null ) { ?>

<h3>فاکتور پرداخت کاربر :  <? echo htmlspecialchars($paymentInfo->payer_name, ENT_COMPAT, 'UTF-8'); ?></h3>
<div class="row-fluid">
				<div class="span12">
				<div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                              <tbody>
								<tr style="background: rgba(53, 110, 255, 0.05);">
								<td>نام و نام خانوادگی</td>
								<td><?php echo htmlspecialchars($paymentInfo->payer_name, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>شماره موبایل</td>
								<td><? echo htmlspecialchars($paymentInfo->payer_mobile, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr style="background: rgba(53, 110, 255, 0.05);">
								<td>ایمیل</td>
								<td><? echo htmlspecialchars($paymentInfo->payer_email, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>موضوع</td>
								<td><? echo htmlspecialchars($paymentInfo->pay_title, ENT_COMPAT, 'UTF-8'); ?> </td>
								</tr>
								<tr style="background: rgba(53, 110, 255, 0.05);">
								<td>توضیحات</td>
								<td> <? echo htmlspecialchars($paymentInfo->pay_description, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>درگاه</td>
								<td><? echo htmlspecialchars($model->portName($paymentInfo->port_id), ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr style="background: rgba(53, 110, 255, 0.05);">
								<td>مبلغ</td>
								<td><? echo round($paymentInfo->price,2) . " ریال "; ?></td>
								</tr>
								<tr>
								<td>کد پیگیری</td>
								<td><? echo htmlspecialchars($paymentInfo->tracking_code, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr style="background: rgba(53, 110, 255, 0.05);">
								<td>وضعیت پرداخت</td>
								<td><? echo htmlspecialchars($paymentInfo->result_message, ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>
								<tr>
								<td>تاریخ پرداخت</td>
								<td><? echo htmlspecialchars(jdate("o/m/j",TinyPaymentHelper::convert_date_to_unix($paymentInfo->log_date)), ENT_COMPAT, 'UTF-8'); ?></td>
								</tr>


                            </tbody>
                            </table>
  </div>
</div> 
						<?php if ($app->getParams()->get('pdfshow') != null && $app->getParams()->get('pdfshow') == 1 ){?>
							<button type="submit" class="btn btn-small"><i class="fa fa-file-pdf-o" aria-hidden="true"></i> خروجی pdf</button>
						<?php }?>
						<input type="hidden" name="task" value="form.pdf2" />
						<input type="hidden" name="id" value="<?php echo $cryptUID ?>" />
					<?php }
					else {
						$msg = 'فاکتوری صادر نشده است . لطفا فرم زیر را پر کنید';
						$link = JRoute::_('index.php?option=com_tinypayment&view=form',false);
						$app->redirect($link, '<h2>'.$msg.'</h2>', $msgType='Error'); 
					}
				?>
				
				
				<?php echo JHtml::_('form.token'); ?>
</form>
<?php TinyPaymentHelper::cRight(); ?>
<?php
if ($session->isActive('uniqId')) { $session->clear('uniqId'); }
?>
