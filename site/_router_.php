<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

function tinypaymentBuildRoute( &$query ) {   
	$segments = array();
	if(isset($query['view']) && !isset($query['layout'])){
		if ($query['view'] = 'form')
			$segments[] = '';		
		unset( $query['view'] );
	}
	else if(isset($query['view']) && isset($query['layout'])){
		$segments[] = $query['layout'];
		unset( $query['view'] );
		unset( $query['layout'] );
	}
	
	return $segments;
}
//=============================================================================================================================
function tinypaymentParseRoute( $segments ) { 
	$vars = array();

	foreach($segments as $k => $name){
		 if($name=='form'){
			$vars['view']='form';
		}
		else if ($name == 'faktor'){
			$vars['view']='form';
			$vars['layout']= 'faktor';
		}
		else if ($name == 'callback'){
			$vars['view']='form';
			$vars['layout']= 'callback';
		}
	}
		
	return $vars;
}
?>
