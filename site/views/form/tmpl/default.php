<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
$model = $this->getModel('form');
$session = JFactory::getSession();
if ($session->isActive('uniqId')) { $session->clear('uniqId'); }
JHtml::_('behavior.formvalidator'); // for client side check javascript
JHtml::_('behavior.keepalive');
$app     = JFactory::getApplication();
//---------------------------------------- config 
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php'; 
$mconfig = new config();
$loadMainConfig = $mconfig->loadMainSettings();
//----------------------------------------
?>
<form class="form-validate" action="<? echo JRoute::_('index.php?option=com_tinypayment&view=form&layout=faktor')?>"  method="post" id="userinfo" name="form">
		<fieldset class="adminform">
			<div class="span12 upload">
			<jdoc:include type="modules" name="position-tinypayment-1" />
			<div class="clearfix"> </div>
			<div class="form-group" style="max-width: 300px;">
				<?php echo $this->form->getLabel('payer_name'); ?>
				<div class="clearfix"></div>
				<?php echo $this->form->getInput('payer_name'); ?>
				<div class="clearfix"> </div>
				<?php echo $this->form->getLabel('payer_mobile'); ?>
				<div class="clearfix"></div>
				<?php echo $this->form->getInput('payer_mobile'); ?>
				<div class="clearfix"> </div>
				<?php echo $this->form->getLabel('payer_email'); ?>
				<div class="clearfix"></div>
				<?php echo $this->form->getInput('payer_email'); ?>
				<div class="clearfix"> </div>
				<?php echo $this->form->getLabel('pay_title'); ?>
				<div class="clearfix"></div>
				<?php echo $this->form->getInput('pay_title'); ?>
				<div class="clearfix"> </div>
				<?php echo $this->form->getLabel('pay_description'); ?>
				<div class="clearfix"></div>
				<?php echo $this->form->getInput('pay_description'); ?>
				<div class="clearfix"> </div>
				<br>
				<div class="clearfix"> </div>
				<select id="pay_port" name="pay_port" type="list" label="درگاه بانک" class="required validate-numeric">
					<?php if(
					!$mconfig->loadPortSettings(1)->active && 
					!$mconfig->loadPortSettings(3)->active && 
					!$mconfig->loadPortSettings(3)->test_mode && 
					!$mconfig->loadPortSettings(9)->active
					) {?>
						<option value="0" class="required" selected="selected">هیچ درگاهی وجود ندارد</option>
						<?php  
							$app->redirect(JURI::root(), '<h2>هیچ درگاهی تنظیم نشده است .</h2>', $msgType='Error'); 
					} 	 
					?>
					<?php if ($mconfig->loadPortSettings(1)->active) {?>
						<option value="1" class="required">ملت</option>
					<?php } ?>
					<?php if ($mconfig->loadPortSettings(3)->active or $mconfig->loadPortSettings(3)->test_mode) {?>
						<option value="3" class="required">زرین پال</option>
					<?php }?>
					<?php if ($mconfig->loadPortSettings(9)->active) {?>
						<option value="9" class="required">سامان</option>
					<?php } ?>
				</select>
				<?php echo $this->form->getLabel('pay_price'); ?><?php echo $this->form->getInput('pay_price'); ?>
				<div class="clearfix margin"></div>
			</div>
			<div class="clearfix"></div>
			<div class="margin">
				<input id="submit" type="submit" value="ارسال" class="validate btn btn-default margin">
				<?php echo JHtml::_('form.token'); ?>
			</div>
			<div class="clearfix"> </div>
			<jdoc:include type="modules" name="position-tinypayment-2" />
		</div> 	
        </fieldset>
</form>
<?php TinyPaymentHelper::cRight(); ?>
