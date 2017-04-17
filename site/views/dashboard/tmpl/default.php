<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE  .'/components/com_tinypayment/helpers/tinypayment.php';
$model = $this->getModel('dashboard');

$app     = JFactory::getApplication();
$user = JFactory::getUser();

$jinput = JFactory::getApplication()->input;
$limitstart = $jinput->get->get('limitstart', '0', 'INT');
if (!isset($limitstart)){
    $limitstart = null;
}
?>
<?php if (!$user->guest) {  ?>
<form action="index.php?option=com_tinypayment&view=dashboard" method="post" id="adminForm" name="adminForm">

<div class="pagination">
<?php echo $this->pagination->getLimitBox(); // namayesh drop-down baraye limit kardan ?>
<br>
<p class="counter pull-right">
<?php echo $this->pagination->getPagesCounter(); //namayesh shomare page?>
</p>
</div>

<div class="row-fluid">
<div class="span12 scrolled">
     <!-- start span12 info boards -->
    <div class="box box-success">
        <div class="box-header with-border">
           <h3 class="box-title">
           <i class="fa fa-file-text-o"></i>
           جدول اطلاعات پرداخت شما
           </h3>

			

	<div class="row-fluid">
		<div class="span12">
			<div class="box-body table-responsive no-padding">
                <table class="table table-hover">
                    <tbody>
						<tr>
							<th> <?php echo JHtml::_('grid.checkall'); ?></th>
							<th>شناسه فاکتور</th>
							<th>عنوان پرداخت</th>
							<th>مبلغ (ریال)</th>
							<th>زمان پرداخت</th>
							<th>وضعیت پرداخت</th>
							<th>وضعیت سفارش</th>
							<th>شماره پیگیری</th>
						</tr>
							

	<?php 

	foreach ($model->getPayment(null) as $i => $value){ ?>
	<tr>
		<td>
			<?php echo JHtml::_('grid.id', $i, 2); ?>
		</td>

		<td>
			<?php echo htmlspecialchars($value->id, ENT_COMPAT, 'UTF-8'); ?>
		</td>

		<td>
			<?php echo '<a href='.JRoute::_('index.php?option=com_tinypayment&view=dashboard&layout=ffaktor&id=' . intval($value->id)).'>'.htmlspecialchars($value->pay_title, ENT_COMPAT, 'UTF-8').'</a><br/>';
			?>
		</td>

		<td>
			<?php echo round($value->price,0); ?>
		</td>

		<td>
			<?php echo htmlspecialchars(jdate("o/m/j",TinyPaymentHelper::convert_date_to_unix($value->last_change_date)), ENT_COMPAT, 'UTF-8'); ?>
		</td>

		<td>
			<?php echo htmlspecialchars($value->result_message, ENT_COMPAT, 'UTF-8'); ?>
		</td>

		<td>
			<?php echo htmlspecialchars(TinyPaymentHelper::orderStatus($value->order_status), ENT_COMPAT, 'UTF-8'); ?>
		</td>

		<td>
			<?php echo htmlspecialchars($value->tracking_code, ENT_COMPAT, 'UTF-8'); ?>
		</td>
	</tr>
<?php } ?>

	

					</tbody>
    			</table>
    	</div>
    </div>
        </div>   
    </div>       
</div>
</div>  
 <input type="hidden" name="limitstart" value="<?php echo $limitstart; ?>" />   			
 <div class="pagination">
<?php echo $this->pagination->getPagesLinks(); // namayesh dokmey-next-prev ?><br>
<p class="counter pull-right"><?php echo $this->pagination->getPagesCounter(); //namayesh shomare page?></p>
</div>
</form>
<?php } else { 
	$link = JRoute::_('index.php?option=com_tinypayment&view=other&layout=other',false);
	$app->redirect($link); 
}
	?>
	<div class="clearfix"> </div>
<?php TinyPaymentHelper::cRight(); ?>
