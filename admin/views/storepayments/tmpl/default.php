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
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');

JHtml::_('formbehavior.chosen', 'select');
require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/jdf.php';
$user = JFactory::getUser();
//==================================================== pagination
$listOrder = $this->escape($this->state->get('list.ordering'));
$listDirn  = $this->escape($this->state->get('list.direction'));
$model = $this->getModel('storepayments');

$all_trans = count($model->statistic('all'));
$success_trans = count($model->statistic('ok'));
$fail_trans = $all_trans - $success_trans ;
?>
<form action="index.php?option=com_tinypayment&view=storepayments" method="post" id="adminForm" name="adminForm">
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
      <div class="clearfix"></div>
<div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">
          <div class="row-fluid">
            <div class="span4 borders a1">
              <h3>تعداد کل تراکنش ها</h3>
              <i class="fa fa-file-text-o"></i> 
              <?php echo intval($all_trans); ?>
            </div>
            <div class="span4 borders a2">
              <h3>تراکنشات ناموفق</h3>
              <i class="fa fa-flask"></i>
              <?php echo intval($fail_trans); ?>
            </div>
            <div class="span4 borders a3">
              <h3>تراکنشات موفق</h3>
              <i class="fa fa-diamond"></i>
                <?php echo intval($success_trans);?>
            </div>
          </div>
            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>
<div class="span6">
			<?php echo JText::_('جستجو بر اساس شماره پیگری'); ?>
			<?php
				echo JLayoutHelper::render(
					'joomla.searchtools.default',
					array('view' => $this)
				);
			?>
		</div>
      <div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">
            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>
            <div class="row-fluid margin">
              <div class="span12 scrolled">
                <!-- start span12 info boards -->
                    <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-file-text-o"></i>جدول اطلاعات پرداخت</h3>
		<?php if (!empty($this->items)) : ?>
			
				<div class="row-fluid">
				<div class="span12">
				<div class="box-body table-responsive no-padding">
                            <table class="table table-hover">
                              <tbody>
								<tr>
									<th> <?php echo JHtml::_('grid.checkall'); ?></th>
									<th>شناسه</th>
									<th>عنوان پرداخت</th>
									<th>مبلغ (ریال)</th>
									<th>درگاه</th>
									<th>زمان پرداخت</th>
									<th>نام پرداخت کننده</th>
									<th>شماره پیگیری</th>
									<th>ایمیل</th>
									<th>وضعیت پرداخت</th>
									<th>وضعیت سفارش</th>
								</tr>
								<?php foreach ($this->items as $i => $row) :
				$link = JRoute::_('index.php?option=com_tinypayment&task=storepayment.edit&id=' . intval($row->id));
				?>             
								<tr>
								<td>
								<?php echo JHtml::_('grid.id', $i, $row->id); ?>
								</td>            
								<td><?php echo intval($row->id); ?> </td>
								<td>
								<a href="<?php echo $link; ?>" title="<?php echo JText::_('مشاهده کامل اطلاعات پرداخت'); ?>">
								<?php echo htmlspecialchars($row->pay_title, ENT_COMPAT, 'UTF-8');?>
								</a>
								</td>
								<td><?php echo round($row->pay_amount,2); ?></td>
								<td><?php echo htmlspecialchars($model->portName($row->pay_port), ENT_COMPAT, 'UTF-8'); ?></td>
								<td><?php echo jdate("o/m/j",$model->convert_date_to_unix($row->pay_time)); ?></td>
								<td><?php echo htmlspecialchars($row->payer_name, ENT_COMPAT, 'UTF-8'); ?></td>
								<td> <?php echo htmlspecialchars($row->tracking_code, ENT_COMPAT, 'UTF-8'); ?></td>
								<td> <?php echo htmlspecialchars($row->payer_email, ENT_COMPAT, 'UTF-8'); ?></td>
								<td style="max-width: 100px;"><?php echo htmlspecialchars($row->pay_status, ENT_COMPAT, 'UTF-8'); ?></td>
								<td><?php echo htmlspecialchars($model->orderStatus($row->order_status), ENT_COMPAT, 'UTF-8'); ?></td>
                                                </tr>
 <?php endforeach; ?>
                            </tbody>
                            </table>
                           
			<?php endif; ?>
                        </div>
</div> 
	<input type="hidden" name="task" value=""/>
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php echo $this->pagination->getListFooter(); ?>

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
