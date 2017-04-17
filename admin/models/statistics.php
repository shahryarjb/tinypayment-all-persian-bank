<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');
require_once JPATH_SITE .'/administrator/components/com_tinypayment/helpers/jdf.php';

class TinyPaymentModelStatistics extends JModelAdmin {
	
	
	public function getTable($type = 'statistics', $prefix = 'TinyPaymentTable', $config = array())
	{
		return JTable::getInstance($type, $prefix, $config);
	}

	public function getForm($data = array(), $loadData = true)
	{
		
	}

//=====================================================================
	protected function loadFormData()
	{
		$data = JFactory::getApplication()->getUserState(
			'com_tinypayment.edit.section.data',
			array()
		);

		if (empty($data)) {
			$data = $this->getItem();
		}

		return $data;
	}
//=====================================================================
	protected function canDelete($record) {
		if( !empty( $record->id ) ) {
			return JFactory::getUser()->authorise( "core.delete", "com_tinypayment.message." . $record->id );
		}
	}
	

//------------------------------------------

	protected function populateState($ordering = 'id', $direction = 'desc')
		{
			// List state information.
			parent::populateState($ordering, $direction);
		}

//------------------------------------------
	protected function getStoreId($id = '')
		{
			return parent::getStoreId($id);
		}
//------------------------------------------
//============================================ load id 
	function loadData ($id) {
		$greoDate =  TinyPaymentHelper::convert_date_to_unix(jalali_to_gregorian(jdate("Y", time(),"","","en"),01,01,'-'));
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('psl.id,pt.price,pt.last_change_date');
		$query->from($db->qn('#__tinypayment_status_log') . 'as psl');
		$query->leftJoin($db->qn('#__tinypayment_transactions') . 'as pt ON pt.id = psl.transaction_id');
		if ($id == 'ok'){
			$query->where(
				$db->qn('psl.result_code') . ' = ' . intval(0) 
				. ' OR '.
				$db->qn('psl.result_code') . ' = ' . intval(1)
				. ' OR '.
				$db->qn('psl.result_code') . ' = ' . intval(100)
			);
		}
		$query->where(
			$db->qn('pt.last_change_date') . ' < ' . $db->q(time()) 
									. ' AND ' . 
			$db->qn('pt.last_change_date') . ' >= ' . $db->q($greoDate)
		);
		$db->setQuery((string)$query); 
		$result = $db->loadObjectlist();
		return $result;
	}
		
	function claculate($input,$stat) { 
		$out1= 0 ;$out2=  0 ;$out3=  0 ;$out4 =  0 ;
		$out5= 0 ;$out6=  0 ;$out7=  0 ;$out8 =  0 ;
		$out9= 0 ;$out10= 0 ;$out11= 0 ;$out12 = 0 ;
		
		switch($input) {
			case 'price':
				foreach($this->loadData($stat) as $d){  // jam kardan mabalegh baraye har mah
					switch(jdate("m", $d->last_change_date,"","","en")) {
						case '01':$out1 += $d->price;break;
						case '02':$out2 += $d->price;break;
						case '03':$out3 += $d->price;break;
						case '04':$out4 += $d->price;break;
						case '05':$out5 += $d->price;break;
						case '06':$out6 += $d->price;break;
						case '07':$out7 += $d->price;break;
						case '08':$out8 += $d->price;break;
						case '09':$out9 += $d->price;break;
						case '10':$out10 += $d->price;break;
						case '11':$out11 += $d->price;break;
						case '12':$out12 += $d->price;break;
					}
				}
				
			break;
			
			case 'count':
				foreach($this->loadData($stat) as $d){  // jam kardan tedade kole trakonesh ha dar har mah (mofagh va namovagh)
						switch(jdate("m", $d->last_change_date,"","","en")) {
							case '01':$out1++;break;
							case '02':$out2++;break;
							case '03':$out3++;break;
							case '04':$out4++;break;
							case '05':$out5++;break;
							case '06':$out6++;break;
							case '07':$out7++;break;
							case '08':$out8++;break;
							case '09':$out9++;break;
							case '10':$out10++;break;
							case '11':$out11++;break;
							case '12':$out12++;break;
						}
				}
			break;
		}
		
		$month = array(
			"far"=>$out1,"ord"=>$out2,"kho"=>$out3,
			"tir"=>$out4,"mor"=>$out5,"sha"=>$out6,
			"meh"=>$out7,"aba"=>$out8,"aza"=>$out9,
			"dey"=>$out10,"bah"=>$out11,"esf"=>$out12
		);
		
		foreach($month as $key => $m){  // gereftan mah haee ke pardakhti dashtan
			//if($m > 0) 
				$value[$key]= $m;
		}
		if (isset($value))
			return $value;
		else 
			return null;
	}
	
	function covertMonth($mon) {  // tabdil mah englisi be farsi
		switch($mon){
			case'far' : $out= '"فروردین"'; break;case'ord' : $out= '"اردیبهشت"'; break;case'kho' : $out= '"خرداد"';break;
			case'tir' : $out= '"تیر"'; break;case'mor' : $out='"مرداد"'; break;case'sha' : $out='"شهریور"';break;
			case'meh' : $out='"مهر"'; break;case'aba' : $out='"آبان"'; break;case'aza' : $out='"آذر"';break;
			case'dey' : $out='"دی"'; break;case'bah' : $out='"بهمن"'; break;case'esf' : $out='"اسفند"';break;
		}
		return $out;
	}
//=============================================
}
