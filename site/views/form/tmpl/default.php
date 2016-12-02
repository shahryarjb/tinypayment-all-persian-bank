<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
$model = $this->getModel('form');
$session = JFactory::getSession();
if ($session->isActive('uniqId')) { $session->clear('uniqId'); }
JHtml::_('behavior.formvalidator'); 
JHtml::_('behavior.keepalive');
$app     = JFactory::getApplication();
?>
<form class="form-validate" action="<? echo JRoute::_('index.php?option=com_tinypayment&view=form&layout=faktor')?>"  method="post" id="userinfo" name="form">
		<fieldset class="adminform">
			<div class="span12 upload">
				<?php echo $this->form->getLabel('payer_name'); ?><?php echo $this->form->getInput('payer_name'); ?>
				<?php echo $this->form->getLabel('payer_mobile'); ?><?php echo $this->form->getInput('payer_mobile'); ?>
				<?php echo $this->form->getLabel('payer_email'); ?><?php echo $this->form->getInput('payer_email'); ?>
				<?php echo $this->form->getLabel('pay_title'); ?><?php echo $this->form->getInput('pay_title'); ?>
				<?php echo $this->form->getLabel('pay_description'); ?><?php echo $this->form->getInput('pay_description'); ?>
				<br>
				<select id="pay_port" name="pay_port" type="list" label="درگاه بانک" class="required validate-numeric">
					<?php if(!$app->getParams()->get('melatstatus') && !$app->getParams()->get('zarinpalstatus') && !$app->getParams()->get('samanstatus')) {?>
						<option value="0" class="required" selected="selected">هیچ درگاهی وجود ندارد</option>
						<?php  
							$app->redirect(JURI::root(), '<h2>هیچ درگاهی تنظیم نشده است .</h2>', $msgType='Error'); 
					} 	 
					?>
					<?php if ($app->getParams()->get('melatstatus')) {?>
						<option value="1" class="required">ملت</option>
					<?php } ?>
					<?php if ($app->getParams()->get('zarinpalstatus')) {?>
						<option value="3" class="required">زرین پال</option>
					<?php }?>
					<?php if ($app->getParams()->get('samanstatus')) {?>
						<option value="9" class="required">سامان</option>
					<?php } ?>
				</select>
				<?php echo $this->form->getLabel('pay_price'); ?><?php echo $this->form->getInput('pay_price'); ?>
				<div class="clearfix margin"></div>
			</div>
			<div class="clearfix"></div>
			<div class="margin">
				</br>
				<input id="submit" type="submit" value="ارسال" class="validate btn btn-small margin">
				<?php echo JHtml::_('form.token'); ?>
			</div>
        </fieldset>
</form>
<?php TinyPaymentHelper::cRight(); ?>
