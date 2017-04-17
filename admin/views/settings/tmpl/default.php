<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted Access');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/customadmin.css');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/custom.css');
JHtml::_('behavior.formvalidation');
JHtml::script('https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.2.2/Chart.bundle.js');
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/jdf.php';
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/config.php';

$app = JFactory::getApplication('site');
$id = $app->input->getInt('id');  
$user = JFactory::getUser();

$config = new config();
$settings = $config->loadMainSettings();
$port_mellat = $config->loadPortSettings(1);
$port_saman = $config->loadPortSettings(9);
$port_zarinpal = $config->loadPortSettings(3);

?>
<form action="<?php echo JRoute::_('index.php?option=com_tinypayment&view=settings'); ?>" method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="form-group">
<?php echo JHtml::_('bootstrap.startTabSet', 'main', array('active' => 'main_settings')); ?>

  <?php echo JHtml::_('bootstrap.addTab', 'main', 'main_settings', JText::_('تنظیمات صفحه', true)); ?>
               
            
  <h5>زمان برگشت</h5>

    <input name="time_back" id="time_back" value="<? echo htmlspecialchars($settings->time_back, ENT_COMPAT, 'UTF-8'); ?>" class="required form-control" required="required" aria-required="true" aria-invalid="false" type="text">

    <div class="alert ">
      زمانی که شما نیاز دارید کاربر به بانک برود و به سایت برگردد. به صورت پیشفرض روی ۱۰ دقیقه می باشد بیشتر از آن می تواند مشکلات امنیتی برای شما درست کند
    </div>
  <hr>
  <h5>نمایش pdf</h5>
  <label class="radio-inline">
  <input type="radio" name="show_pdf" value="1" <?php echo ($settings->show_pdf) == 1 ? 'checked="checked"' : ''; ?>> بله
  </label>
  <label class="radio-inline">
  <input type="radio" name="show_pdf" value="0" <?php echo ($settings->show_pdf) == 0 ? 'checked="checked"' : ''; ?>> خیر
  </label>
  <div class="clearfix"></div>
  <br>
  <div class="alert ">
      این تنظیمات برای نمایش دادن یا نمایش ندادن دکمه خروجی  PDF در یک فاکتور می باشد . شما می توانید این مورد را فعال یا غیر فعال کنید
  </div>
  <hr>             
                <h5>وضعیت کد کپچای گوگل </h5>
                <input type="radio" name="captcha" value="1" <? echo ($settings->captcha) == 1 ? 'checked="checked"' : ''; ?>> بله 
                <input type="radio" name="captcha" value="0" <? echo ($settings->captcha) == 0 ? 'checked="checked"' : ''; ?>> خیر
<br>
<br>
                کلید عمومی <input name="public_key" id="public_key" value="<? echo htmlspecialchars($settings->public_key, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><p>
                کلید خصوصی <input name="private_key" id="private_key" value="<? echo htmlspecialchars($settings->private_key, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><p>
      <div class="alert ">
      کد کپچا برای جلوگیری از حملات ربات ها به فرم در وب سایت شما می باشد . ما برای امنیت بیشتر از سیستم reCaptcha گوگل استفاده کردیم . برای دریافت کد عمومی و خصوصی می توانید به وب سایت google.com/recaptcha مراجعه کنید
      </div>
<hr> 
  <h5>نمایش ایمیل </h5>
                <input type="radio" name="show_email" value="1" <? echo ($settings->show_email) == 1 ? 'checked="checked"' : ''; ?>> بله
                <input type="radio" name="show_email" value="0" <? echo ($settings->show_email) == 0 ? 'checked="checked"' : ''; ?>> خیر
<div class="clearfix"></div>
<br>
<div class="alert ">
      این تنظیمات برای نمایش دادن یا نمایش ندادن دکمه خروجی  email در یک فاکتور می باشد . شما می توانید این مورد را فعال یا غیر فعال کنید
  </div>
<hr>                
  <h5> بوت استرپ </h5>
                <input type="radio" name="bootstrap" value="1" <? echo ($settings->bootstrap) == 1 ? 'checked="checked"' : ''; ?>> بله 
                <input type="radio" name="bootstrap" value="0" <? echo ($settings->bootstrap) == 0 ? 'checked="checked"' : ''; ?>> خیر 
             <div class="clearfix"></div>
             <br> 
         <div class="alert ">
توجه : متاسفانه هنوز خیلی از کاربران جوملایی از قالب های تاریخ گذشته استفاده می کنند و به همین ترتیب در این قالب ها فریم ورک بوتسترپ وجود ندارد و شمایل افزونه مذکور به مشکل برخورد می کند. اگر در وب سایت به مشکل برخورد کردید و فرم ها و موارد مربوط به داشبرد شکل نا مناسبی پیدا کرده است در تنظیمات روی بله قرار دهید  
</div>   
    <?php echo JHtml::_('bootstrap.endTab'); ?>


    <?php echo JHtml::_('bootstrap.addTab', 'main', 'port_settings', JText::_('تنظیمات درگاه ها', true)); ?>
        <?php echo JHtml::_('bootstrap.startTabSet', 'sub_main', array('active' => 'saman')); ?>
 
            <?php echo JHtml::_('bootstrap.addTab', 'sub_main', 'saman', JText::_('درگاه سامان', true)); ?>
            <h5> فعال بودن درگاه </h5>
                        <input type="radio" name="active9" value="1" <? echo ($port_saman->active) == 1 ? 'checked="checked"' : ''; ?>> بله
                        <input type="radio" name="active9" value="0"  <? echo ($port_saman->active) == 0 ? 'checked="checked"' : ''; ?>> خیر
                        <hr>
                        <h5>نام کاربری : </h5>
                        <input name="username9" id="username9" value="<? echo htmlspecialchars($port_saman->username, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
                         <h5>کلمه عبور : </h5>

                         <input name="password9" id="password9" value="<? echo htmlspecialchars($port_saman->password, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
                         <h5>شماره ترمینال : </h5>
                      <input name="terminalcode9" id="terminalcode9" value="<? echo htmlspecialchars($port_saman->terminal_code, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
            <?php echo JHtml::_('bootstrap.endTab'); ?>


            <?php echo JHtml::_('bootstrap.addTab', 'sub_main', 'mellat', JText::_('درگاه ملت ', true)); ?>
                      <h5>فعال بودن درگاه </h5>
                        <input type="radio" name="active1" value="1" <? echo ($port_mellat->active) == 1 ? 'checked="checked"' : ''; ?>> بله

                        <input type="radio" name="active1" value="0" <? echo ($port_mellat->active) == 0 ? 'checked="checked"' : ''; ?>> خیر
                        <hr>
                        <h5>نام کاربری : </h5>
                       <input name="username1" id="username1" value="<? echo htmlspecialchars($port_mellat->username, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text">
                       <h5>کلمه عبور : </h5>
                         <input name="password1" id="password1" value="<? echo htmlspecialchars($port_mellat->password, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
                      <h5>شماره ترمینال : </h5>
                      <input name="terminalcode1" id="terminalcode1" value="<? echo htmlspecialchars($port_mellat->terminal_code, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
            <?php echo JHtml::_('bootstrap.endTab'); ?>


            <?php echo JHtml::_('bootstrap.addTab', 'sub_main', 'zarinpall', JText::_('درگاه زرین پال ', true)); ?>
                        <h5> فعال بودن درگاه </h5>
                        <input type="radio" name="active3" value="1" <? echo ($port_zarinpal->active) == 1 ? 'checked="checked"' : ''; ?>> بله
                        <input type="radio" name="active3" value="0" <? echo ($port_zarinpal->active) == 0 ? 'checked="checked"' : ''; ?>> خیر

                        <hr>

                           <h5>فعال بودن درگاه تست : </h5>  
                        <input type="radio" name="testmode3" value="1" <? echo ($port_zarinpal->test_mode) == 1 ? 'checked="checked"' : ''; ?>> بله
                        <input type="radio" name="testmode3" value="0" <? echo ($port_zarinpal->test_mode) == 0 ? 'checked="checked"' : ''; ?>> خیر
                     
                     <h5>شماره ترمینال : </h5>
                      <input name="terminalcode3" id="terminalcode3" value="<? echo htmlspecialchars($port_zarinpal->terminal_code, ENT_COMPAT, 'UTF-8'); ?>" class="required" required="required" aria-required="true" aria-invalid="false" type="text"><br>
            <?php echo JHtml::_('bootstrap.endTab'); ?>


        <?php echo JHtml::_('bootstrap.endTabSet'); ?>
    <?php echo JHtml::_('bootstrap.endTab'); ?>


<?php echo JHtml::_('bootstrap.endTabSet'); ?>
<input type="hidden" name="task" value="storepayment.edit" />
<?php echo JHtml::_('form.token'); ?>
</div>
</form>
<div class="clearfix"></div>
<div class="row-fluid margin">
<?php TinyPaymentHelper::cRight(); ?>
</div>