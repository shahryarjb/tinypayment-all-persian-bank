<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die('Restricted access');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/customadmin.css');
JHtml::stylesheet(JURI::root().'components/com_tinypayment/ui/dist/css/custom.css');
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

JHtml::_('behavior.formvalidation');
require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/jdf.php';

$app = JFactory::getApplication('site')->input;
$id = $app->get('id');
$model = $this->getModel('storepayment');
$peymentInfo= $model->getPaymentInfo($id);
$user = JFactory::getUser();

?>
<form action="<?php echo JRoute::_('index.php?option=com_tinypayment&layout=edit&id=' . (int) $this->item->id); ?>"
    method="post" name="adminForm" id="adminForm" class="form-validate">
<div class="container-fluid">
  <div class="row-fluid">
    <div class="span12">
    <div class="row-fluid">
    <div class="span3 mainsid">
      <div class="span12 logo-admin">

            <?php TinyPaymentHelper::imgAdmin(); ?>

              <div class="clearfix"></div>
              <div class="row-fluid margin"></div>
              <div class="text-back-top">
 	<?php if (!$user->guest) {  
                echo htmlspecialchars($user->name, ENT_COMPAT, 'UTF-8');
              } ?>
              </div>

      </div>

<!--  <div class="span12"> -->
  <div class="row-fluid">
      <?php TinyPaymentHelper::menuAdmin(); ?>
      <!-- </div> -->
      </div>
    </div><!-- sidbar -->

    <div class="span9 intab">
      <div class="row-fluid">
        <div class="span12">
          <div class="callout callout-info">
          <?php 
               $t = ((time()-$peymentInfo['log_date'])/3600);
               $min = number_format((float)$t, 3);
               $hour = floor($min);
               $minute = round(60*($min - $hour));       
          ?>
          <h4><i class="fa fa-info"></i> توجه:</h4>
مدیریت محترم توجه داشته باشید از زمان ثبت این ترانکش  <?php echo htmlspecialchars($hour, ENT_COMPAT, 'UTF-8');?> ساعت <?php echo htmlspecialchars($minute, ENT_COMPAT, 'UTF-8');?> دقیقه میگذرد <?php if($peymentInfo['edit_time'] != 0 ) echo 'و آخرین ویرایش شما در تاریخ '.jdate("o/m/j",$model->convert_date_to_unix($peymentInfo['edit_time'])).'  بوده است.  '; ?>
          </div>
        </div>
      </div>
      <div class="clearfix"></div>


<div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">
          <div class="row-fluid">
            <div class="span4 borders a1">
              <h3>شناسه تراکنش</h3>
              <i class="fa fa-file-text-o"></i> 
              <?php echo htmlspecialchars($peymentInfo['id_transaction'], ENT_COMPAT, 'UTF-8');?>
            </div>
            <div class="span4 borders a2">
              <h3>تاریخ ویرایش</h3>
              <i class="fa fa-flask"></i>
			<?php 
			if($peymentInfo['edit_time'] != 0 ) 
				echo jdate("o/m/j",$model->convert_date_to_unix($peymentInfo['edit_time']));
			else 
				echo 'ویرایش نشده'; 
			?>

            </div>
            <div class="span4 borders a3">
              <h3>شناسه فرم</h3>
              <i class="fa fa-diamond"></i>
                <?php $jinput = JFactory::getApplication()->input; echo $jinput->getInt('id', 0); ?>
            </div>
          </div>
            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>


      <div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">

            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>
            <div class="row-fluid margin">
              <div class="span12">
                <!-- start span12 info boards -->
                    <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-file-text-o"></i>جدول اطلاعات پرداخت</h3>
<div class="row-fluid">
<div class="span6">
<div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                              <tbody>
                              <tr>
	                                <th>ردیف</th>
	                                <th>موضوع</th>
	                                <th>توضیحات</th>
	                   </tr>             
	                   <tr>             
                                          <td>1</td>
                                          <td>نام پرداخت کننده</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['payer_name'], ENT_COMPAT, 'UTF-8'); ?></td>
                                </tr>

                                 <tr style="background-color: rgba(3, 169, 244, 0.05);">             
                                          <td>2</td>
                                          <td>شناسه ip</td>
                                          <td><?php echo (htmlspecialchars($peymentInfo['payer_ip'])); ?></td>
                                </tr> 

                                 <tr>             
                                          <td>3</td>
                                          <td>موبایل پرداخت کننده</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['payer_mobile'], ENT_COMPAT, 'UTF-8');?></td>
                                </tr>
                                
                                 <tr style="background-color: rgba(3, 169, 244, 0.05);">         
                                          <td>4</td>
                                          <td>ایمیل پرداخت کننده</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['payer_email'], ENT_COMPAT, 'UTF-8');?></td>
                                </tr>

                                <tr>
                                          <td>5</td>
                                          <td>عنوان پرداخت</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['pay_title'], ENT_COMPAT, 'UTF-8');?></td>
                                </tr>  

                                 <tr style="background-color: rgba(3, 169, 244, 0.05);">            
                                 	      <td>6</td>            
                                          <td>توضیحات پرداخت کننده</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['pay_description'], ENT_COMPAT, 'UTF-8'); ?></td>
                                </tr>  

                                <tr>
                                          <td>7</td>
                                          <td>تاریخ پرداخت</td>
                                          <td><?php echo jdate("o/m/j",$model->convert_date_to_unix($peymentInfo['log_date'])); ?></td>
                                </tr>
                                
                                 <tr style="background-color: rgba(3, 169, 244, 0.05);">             
                                          <td>8</td>
                                          <td>شماره کارت</td>
                                          <td style="direction: ltr"><?php if ( ($peymentInfo['cardNumber'] != null) ||  ($peymentInfo['cardNumber'] != '') ) echo htmlspecialchars($peymentInfo['cardNumber'], ENT_COMPAT, 'UTF-8'); else echo  'شماره کارت مشخص نمی باشد '; ?></td>
                                </tr>

                                <tr>             
                                          <td>9</td>
                                          <td>انتخاب درگاه</td>
                                          <td>بانک <?php echo htmlspecialchars($model->portName($peymentInfo['port_id']), ENT_COMPAT, 'UTF-8'); ?></td>
                                </tr> 

                                <tr style="background-color: rgba(3, 169, 244, 0.05);">             
                                          <td>10</td>
                                          <td>مبلغ پرداخت</td>
                                          <td><?php echo round($peymentInfo['price'],2); ?> ریال</td>
                                </tr>
                                <tr>             
                                          <td>11</td>
                                          <td>وضعیت پرداخت</td>
                                          <td><?php echo htmlspecialchars($peymentInfo['result_message'], ENT_COMPAT, 'UTF-8'); ?></td>
                                </tr>      
                            </tbody></table>
                        </div>
</div> 

<div class="span6">
              <div class="form-horizontal">
              <div class="row-fluid">
              <div class="span4">
<?php echo $this->form->getLabel('order_status'); ?>
</div>
<div class="span6">
<?php echo $this->form->getInput('order_status'); ?>
</div>
</div>
</br>
<?php echo $this->form->getLabel('admin_description'); ?>		
<?php echo $this->form->getInput('admin_description'); ?>

	</div>
</div>

</div>
          </div>
          <!-- /.box -->
              </div> <!-- span12 info boards-->
            </div>
            </div>
        </div>
      </div>  
    </div><!-- content -->
    </div>
    </div> <!-- Main span12 -->
  </div>  <!-- row-fluid -->
  <div class="clearfix"></div>
      <div class="row-fluid margin">
         <?php TinyPaymentHelper::cRight(); ?>
      </div>
</div>  <!-- Main container-fluid -->
<div class="clearfix"></div>
<div class="margin"></div>
	<input type="hidden" name="task" value="storepayment.edit" />
	<?php echo JHtml::_('form.token'); ?>
	 <input type="hidden" name="boxchecked" value="0" />
</form>
