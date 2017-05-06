<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
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
$app = JFactory::getApplication('site');
$id = $app->input->getInt('id');  
$model = $this->getModel('statistics');
$user = JFactory::getUser();

$all_data = $model->loadData('all');
$ok_data = $model->loadData('ok');
$tedad_kol = count($all_data); // tedad kol dar kol sal
$tedad_mofagh = count($ok_data); // tedad mofagh dar kol sal
$tedad_na_mofagh = count($all_data) - count($ok_data); // tedad cancel shode dar kol sal

//price_ok bar asase har mah
$mabaleghe_mofagh = $model->claculate('price','ok');
//count_all bar asase har mah
$tedad_kol_mah = $model->claculate('count','all');
//count_ok
$tedad_mofagh_mah = $model->claculate('count','ok');

if ($tedad_mofagh_mah != null ) {
	//value_all
	$tedad_trakonesh_hame_mah = array_values($tedad_kol_mah);
	//value_ok
	$jame_tedad_mofagh_mah = array_values($tedad_mofagh_mah);
}
if ($mabaleghe_mofagh != null ) {
	//value_price
	$mabaleghe_mofagh_mah = array_values($mabaleghe_mofagh);
	//allpricestatic
	$jame_mablagh_kol = array_sum($mabaleghe_mofagh); // mablaghe kol
}
else {
	$jame_mablagh_mofagh_ma = '0';
}


?>
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
مدیریت محترم تمامی موارد نمایش داده شده در این بخش بر اساس ماه و سال می باشد. برای نمایش آمار کلی به صفحه ذخیره پرداخت ها مراجعه فرمایید.
</br>
توجه : آمار نمایش داده شده برای پنج ماه آخر می باشد.
          </div>
        </div>
      </div>
      <div class="clearfix"></div>


<div class="clearfix"></div>
      <div class="row-fluid margin">
        <div class="span12">
          <div class="row-fluid">
            <div class="span4 borders a1">
              <h3>مبلغ کل</h3>
              <i class="fa fa-file-text-o"></i> 
                <?php echo $jame_mablagh_kol;?>
            </div>
            <div class="span4 borders a2">
              <h3>پرداخت ها</h3>
              <i class="fa fa-flask"></i>
              <?php echo $tedad_kol;?>
            </div>
            <div class="span4 borders a3">
              <h3>پرداخت های موفق</h3>
              <i class="fa fa-diamond"></i>
                <?php echo $tedad_mofagh; ?>
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
              <div class="span12 scrolled">
                <!-- start span12 info boards -->
                    <div class="box box-success">
            <div class="box-header with-border">
              <h3 class="box-title"><i class="fa fa-file-text-o"></i> اطلاعات پرداخت ها به صورت گرافیکی</h3>
              <hr>
              <form action="index.php?option=com_tinypayment&view=storepayments" method="post" id="adminForm" name="adminForm">
			
				<div class="row-fluid">
				<div class="span12">
                         
                            <h2 style="font-weight: normal;"><i class="fa fa-check-square-o" aria-hidden="true"></i> نمایش آمار گرافیکی از تعداد پرداخت ها</h2>
                            <canvas id="allCharts" width="1109" height="300" style="display: block; width: 100%; height: 300px;"></canvas>
                            </div>
                          </div>
                          <div class="clearfix"></div>
                          <div class="row-fluid">
                            <div class="span12">
                            <h2 style="font-weight: normal;"><i class="fa fa-check-square-o" aria-hidden="true"></i> آمار گرافیکی از مبالغ واریزی موفق</h2>
                             <canvas id="Chartonpage" width="1109" height="300" style="display: block; width: 100%; height: 300px;"></canvas>
							
                  </div>
                  </div> 

<?php
    if ($mabaleghe_mofagh != null) {
      foreach (array_keys($mabaleghe_mofagh) as $mon){
        $month[] = $model->covertMonth($mon);
      }
      //allpricemonth
      $all_month_ok = implode(",",$month);
      //allprice
      $all_month_price = implode(",",$mabaleghe_mofagh_mah);    
    }
    //-------------------------------------------------------
    //-------------------------------------------------------
    if ($tedad_mofagh_mah != null) {
      foreach (array_keys($tedad_mofagh_mah) as $mon){
        $month_all[] = $model->covertMonth($mon);
      }
      //allstorepaymentmonth
      $name_mah_mofagh = implode(",",$month_all);
      //allstorepaymentvalue
      $tedad_mah_mofagh =implode(",",$tedad_mofagh_mah);
      //allpaymentokyvalue
      $tedad_trakonesh_ok = implode(",",array_values($tedad_mofagh_mah));
     
       foreach($tedad_kol_mah as $key => $all){
        $month_failed[] =$tedad_mofagh_mah[$key] - $all; 
      }

      $failmonthvalue = implode(",",array_values($month_failed));
    }
   
?>
<script>
<?php if (isset($name_mah_mofagh) && isset($tedad_mah_mofagh) && isset($tedad_trakonesh_ok) && isset($failmonthvalue)){?>
Chart.defaults.global.hover.mode = 'single';
var ctx = document.getElementById("allCharts");
var myChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: [<?php echo $name_mah_mofagh; ?>],
        datasets: [{
            label: 'تعداد کل پرداختی ها بر اساس پرداخت کننده',
            data: [<?php echo implode(",",$tedad_kol_mah); ?>],
            fill: true,
            lineTension: 0.1,
  backgroundColor: "rgba(255, 0, 0, 0.07)",
            borderColor: "rgba(255, 0, 0, 0.07)",
            borderWidth: 2,
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10
        },
        {
                type: 'line',
                label: 'تعداد پرداخت های موفق',
                data: [<?php echo $tedad_trakonesh_ok; ?>],
                backgroundColor: "rgba(75,192,192,0.4)",
                 fill: true,
            lineTension: 0.1,
            borderColor: "rgba(255, 0, 0, 0.07)",
            borderWidth: 3,
            borderCapStyle: 'butt',
            borderDash: [],
            borderDashOffset: 0.0,
            borderJoinStyle: 'miter',
            pointBorderColor: "rgba(75,192,192,1)",
            pointBackgroundColor: "#fff",
            pointBorderWidth: 1,
            pointHoverRadius: 5,
            pointHoverBackgroundColor: "rgba(75,192,192,1)",
            pointHoverBorderColor: "rgba(220,220,220,1)",
            pointHoverBorderWidth: 2,
            pointRadius: 1,
            pointHitRadius: 10
        },
        {
                type: 'line',
                label: 'تعداد پرداخت های نا موفق',
                data: [<?php echo $failmonthvalue ?>],
                
            }
         ]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
<?php }?>

<?php if (isset($all_month_ok) && isset($all_month_price)){?>
var ctxs = document.getElementById("Chartonpage");
var myCharts = new Chart(ctxs, {
    type: 'bar',
    data: {
        labels: [<?php echo $all_month_ok; ?>],
        datasets: [{
            label: 'مبالغ واریزی',
                data: [<?php echo $all_month_price; ?>],
            backgroundColor: [
                'rgba(255, 99, 132, 0.2)',
                'rgba(54, 162, 235, 0.2)',
                'rgba(255, 206, 86, 0.2)',
                'rgba(75, 192, 192, 0.2)',
                'rgba(153, 102, 255, 0.2)',
                'rgba(255, 159, 64, 0.2)'
            ],
            borderColor: [
                'rgba(255,99,132,1)',
                'rgba(54, 162, 235, 1)',
                'rgba(255, 206, 86, 1)',
                'rgba(75, 192, 192, 1)',
                'rgba(153, 102, 255, 1)',
                'rgba(255, 159, 64, 1)'
            ],
            borderWidth: 1
        }]
    },
    options: {
        scales: {
            yAxes: [{
                ticks: {
                    beginAtZero:true
                }
            }]
        }
    }
});
<?php }?>
</script>

	<input type="hidden" name="task" value=""/>
    <input type="hidden" name="boxchecked" value="0"/>
    <?php echo JHtml::_('form.token'); ?>
</form>



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
