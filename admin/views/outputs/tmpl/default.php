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
JHtml::stylesheet('https://maxcdn.bootstrapcdn.com/font-awesome/4.5.0/css/font-awesome.min.css');
JHtml::script('https://ajax.googleapis.com/ajax/libs/angularjs/1.4.8/angular.min.js');

$app = JFactory::getApplication('site');
$id = $app->input->getInt('id');	
$model = $this->getModel('outputs');

require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/jdf.php';
$user = JFactory::getUser();

$lastDate = $model->getLastDate();

?>
<html ng-app="App" >
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
          <h4><i class="fa fa-info"></i> توجه:</h4>
مدیریت محترم توجه کنید . در صورتی که نیاز به دانلود یک فاکتور دارید باید به قسمت ذخیره پرداخت ها مراجعه فرمایید. در این بخش تمامی خروجی ها بر اساس
تاریخ انتخاب شده دریافت می گردد.
          </div>
        </div>
      </div>
      <div class="clearfix"></div>


<div class="clearfix"></div>
     <!-- <div class="row-fluid margin">
        <div class="span12">
          <div class="row-fluid">
            <div class="span4 borders a1">
              <h3>مبلغ کل</h3>
              <i class="fa fa-file-text-o"></i> 
                <?php echo $allpricestatic;?>
            </div>
            <div class="span4 borders a2">
              <h3>پرداخت ها</h3>
              <i class="fa fa-flask"></i>
              <?php echo $alldatastatic;?>
   
                <?php echo $allpriceokystatic; ?>
            </div>
          </div> -->
            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>


      <div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">
<div>
	<?php 
		if ($lastDate != null) {
			echo'<div class="bg-warnings-site"><p>';
			$msg1= 'آخرین گزارشی که گرفته شد در تاریخ  ';
			echo $msg1.jdate("o/m/j در ساعت H:i:s",$lastDate['create_time']).'  ';
			echo 'و بازه زمانی گزارش  ';
			echo 'از تاریخ  '.jdate("o/m/j",$lastDate['start_date']);
			echo 'تا تاریخ '.jdate("o/m/j",$lastDate['end_date']);
			echo 'می باشد';
			echo '</p></div>';
		}
		else {
			echo'<div class="bg-warnings-site"><p>';
			echo 'هیچ گزارشی ایجاد نشده است';
			echo '</p></div>';
		}
	?>
</div>
            <div class="clearfix"></div>
            <div class="margin"></div>
            <div class="clearfix"></div>
            <div class="row-fluid margin">
              <div class="span12 scrolled">
                <!-- start span12 info boards -->
                    <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-file-text-o"></i> دریافت خروجی بر اساس زمان انتخابی</h3>
              <hr>
              <form action="index.php?option=com_tinypayment&view=outputs" method="post" id="adminForm" name="adminForm">
	<div class="row-fluid">
		<div class="span6">
		</div>
	</div>
 <?php echo $this->form->getLabel('start'); ?> <?php echo $this->form->getInput('start'); ?>
 <?php echo $this->form->getLabel('end'); ?> <?php echo $this->form->getInput('end'); ?>
<br>
<div id="selectbox"></div>
<br>
<div ng-controller="ctrl">
    <select  ng-model="changeType" ng-change="changedValue(changeType)" >
      <option value="outputs.csv2">CSV</option>
      <option value="outputs.pdf2">PDF</option>
    </select>
    <input type="hidden" id="task" name="task" value="outputs.csv2" />  
</div>

<div class="clearfix"></div>
<input type="submit" name="submit" value="دریافت" class="btn btn-small btn-success"/>
 
	<input type="hidden" name="boxchecked" value="0"/>
	<?php echo JHtml::_('form.token'); ?>
</form>
</html>
<script>
angular.module("App",[]).controller("ctrl",['$scope',function($scope){
$scope.changedValue = function(item){       
  var myEl = angular.element( document.querySelector( '#task' ) );
  myEl.val(item);
}
}]);
</script>

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
</html>