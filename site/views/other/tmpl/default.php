<?php
/**
 * @copyright   Copyright (C) 2016 Open Source Matters, Inc. All rights reserved. ( https://trangell.com )
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 * @subpackage  com_MiniUniversity
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE  .'/components/com_tinypayment/helpers/tinypayment.php';
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
$accessacl = JFactory::getUser();
if (!$accessacl->guest) {
  $app = JFactory::getApplication(); 
  $app->redirect(JURI::root(), '<h2>شما به این صفحه دسترسی ندارید.</h2>', $msgType='Error'); 
}
?>

<div class="margin"></div> 
                    <div class="container-fluid">
                      <div class="row-fluid">
                        <div class="span12">
                        <div class="span12" style="text-align: center;">
                            <i class="fa fa-warning" aria-hidden="true" style="font-size: 200px;color: rgba(255, 0, 0, 0.68);"></i>
                        </div>

                        <p style="text-align: center;">
مهمان/کاربر محترم ما به این موضوع واقف هستیم که شما به اکانت کاربری خودتان وارد نشده اید یا  مدت زمانی را از وب سایت استفاده نکردید و اکانت شما از حالت لاگین خارج شده است برای دسترسی به این بخش لطفا وارد سایت شوید.
                        </p>

                        <p style="text-align: center;">
توجه  : این موضوع فقط برای امنیت اطلاعات شما می باشد. لطفا از ما دلخور نشوید بهترین ها همیشه برای شما در نظر گرفته می شود. 
                       </p>

                       <p style="text-align: center;">
اگر در ورود به سایت دچار مشکل شده اید حتما با مدیریت سایت در ارتباط باشید . لطفا در حفظ اطلاعات خود کوشا باشید
                       </p>
                       <div class="clearfix"></div>
                       <div class="row-fluid">
                        <div class="span12" style="text-align: center;">
                            <a href="index.php?option=com_users&view=login" class="btgallery"><i class="fa fa-unlock" aria-hidden="true"></i> وارد شدن به سایت</a>                                         
                            <a href="index.php" class="btgallery"><i class="fa fa-paper-plane" aria-hidden="true"></i> برگشت به صفحه اصلی</a>                                         
                        <div class="clearfix"></div>
                        <div class="margin"></div>
                        </div>
                       </div>   
                  <div class="clearfix"></div>
                  <div class="margin"></div>
                  </div>
                </div>
            </div>

  <div class="clearfix"> </div>
<?php TinyPaymentHelper::cRight(); ?>