<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die;

use Joomla\Filter\InputFilter;
use Joomla\String\StringHelper;

class checkHack extends InputFilter {
	public static function checkNum ($input) {
		if (is_numeric($input)){
			if (preg_match('/[0-9]/',htmlspecialchars($input, ENT_QUOTES, 'UTF-8'))){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public static function checkString ($input) {
		if (strlen(utf8_decode($input)) <= 60){
			$newInput = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
			$source = (string) (new InputFilter)->remove($newInput);
			if(!preg_match('/[\~\!\@\#\$\%\^\&\*\)\(\_\-\=\+\/\::\|\'\"\;\:\?\`\]\[\}\{]/',$source)){
					return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public static function checkEmail ($input) {
		$newInput = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
		if (preg_match('/\b[A-Z0-9\._-]+@[A-Z0-9\.-]+\.[A-Z]{2,4}\b/i', (string) $newInput)){
			if(!preg_match('/\beval\b\s*(.*)\(\s*base64_decode/i',$newInput)){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public static function checkMobile ($input) {
		if (is_numeric($input)){
			$source = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
			if ((strlen($source) === 11)){
				if (preg_match('/[0]{1}\d{10}/i', (string) $source)){
					return true;
				}
				else {
					return false;
				}
			}
			else {
				return false;
			}
		}
		else {
				return false;
		}
	}
	
	public static function checkAlphaNumberic ($input) {
		$newInput = htmlspecialchars($input, ENT_QUOTES, 'UTF-8');
		$source = (string) (new InputFilter)->remove($newInput);
		if(!preg_match('/[\~\!\@\#\$\%\^\&\*\)\(\_\-\=\+\/\::\|\'\"\;\:\?\`\]\[\}\{]/', $source)){
			if(!preg_match('/\beval\b\s*(.*)\(\s*base64_decode/i',$source)){
				return true;
			}
			else {
				return false;
			}
		}
		else {
			return false;
		}
	}

	public static function strip($val) {
	   $val = preg_replace('/([\x00-\x08][\x0b-\x0c][\x0e-\x20])/', '', $val);
	   $search = 'abcdefghijklmnopqrstuvwxyz';
	   $search .= 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
	   $search .= '1234567890!@#$%^&*()';
	   $search .= '~`";:?+/={}[]-_|\'\\';
	   for ($i = 0; $i < strlen($search); $i++) {
		  $val = preg_replace('/(&#[x|X]0{0,8}'.dechex(ord($search[$i])).';?)/i', $search[$i], $val); 
		  $val = preg_replace('/(&#0{0,8}'.ord($search[$i]).';?)/', $search[$i], $val); 
	   }

	   $ra1 = Array('\/([ \t\r\n]+)?javascript', '\/([ \t\r\n]+)?vbscript', ':([ \t\r\n]+)?expression', '<([ \t\r\n]+)?applet', '<([ \t\r\n]+)?meta', '<([ \t\r\n]+)?xml', '<([ \t\r\n]+)?blink', '<([ \t\r\n]+)?link', '<([ \t\r\n]+)?style', '<([ \t\r\n]+)?script', '<([ \t\r\n]+)?embed', '<([ \t\r\n]+)?object', '<([ \t\r\n]+)?iframe', '<([ \t\r\n]+)?frame', '<([ \t\r\n]+)?frameset', '<([ \t\r\n]+)?ilayer', '<([ \t\r\n]+)?layer', '<([ \t\r\n]+)?bgsound', '<([ \t\r\n]+)?title', '<([ \t\r\n]+)?base');
	   $ra2 = Array('onabort([ \t\r\n]+)?=', 'onactivate([ \t\r\n]+)?=', 'onafterprint([ \t\r\n]+)?=', 'onafterupdate([ \t\r\n]+)?=', 'onbeforeactivate([ \t\r\n]+)?=', 'onbeforecopy([ \t\r\n]+)?=', 'onbeforecut([ \t\r\n]+)?=', 'onbeforedeactivate([ \t\r\n]+)?=', 'onbeforeeditfocus([ \t\r\n]+)?=', 'onbeforepaste([ \t\r\n]+)?=', 'onbeforeprint([ \t\r\n]+)?=', 'onbeforeunload([ \t\r\n]+)?=', 'onbeforeupdate([ \t\r\n]+)?=', 'onblur([ \t\r\n]+)?=', 'onbounce([ \t\r\n]+)?=', 'oncellchange([ \t\r\n]+)?=', 'onchange([ \t\r\n]+)?=', 'onclick([ \t\r\n]+)?=', 'oncontextmenu([ \t\r\n]+)?=', 'oncontrolselect([ \t\r\n]+)?=', 'oncopy([ \t\r\n]+)?=', 'oncut([ \t\r\n]+)?=', 'ondataavailable([ \t\r\n]+)?=', 'ondatasetchanged([ \t\r\n]+)?=', 'ondatasetcomplete([ \t\r\n]+)?=', 'ondblclick([ \t\r\n]+)?=', 'ondeactivate([ \t\r\n]+)?=', 'ondrag([ \t\r\n]+)?=', 'ondragend([ \t\r\n]+)?=', 'ondragenter([ \t\r\n]+)?=', 'ondragleave([ \t\r\n]+)?=', 'ondragover([ \t\r\n]+)?=', 'ondragstart([ \t\r\n]+)?=', 'ondrop([ \t\r\n]+)?=', 'onerror([ \t\r\n]+)?=', 'onerrorupdate([ \t\r\n]+)?=', 'onfilterchange([ \t\r\n]+)?=', 'onfinish([ \t\r\n]+)?=', 'onfocus([ \t\r\n]+)?=', 'onfocusin([ \t\r\n]+)?=', 'onfocusout([ \t\r\n]+)?=', 'onhelp([ \t\r\n]+)?=', 'onkeydown([ \t\r\n]+)?=', 'onkeypress([ \t\r\n]+)?=', 'onkeyup([ \t\r\n]+)?=', 'onlayoutcomplete([ \t\r\n]+)?=', 'onload([ \t\r\n]+)?=', 'onlosecapture([ \t\r\n]+)?=', 'onmousedown([ \t\r\n]+)?=', 'onmouseenter([ \t\r\n]+)?=', 'onmouseleave([ \t\r\n]+)?=', 'onmousemove([ \t\r\n]+)?=', 'onmouseout([ \t\r\n]+)?=', 'onmouseover([ \t\r\n]+)?=', 'onmouseup([ \t\r\n]+)?=', 'onmousewheel([ \t\r\n]+)?=', 'onmove([ \t\r\n]+)?=', 'onmoveend([ \t\r\n]+)?=', 'onmovestart([ \t\r\n]+)?=', 'onpaste([ \t\r\n]+)?=', 'onpropertychange([ \t\r\n]+)?=', 'onreadystatechange([ \t\r\n]+)?=', 'onreset([ \t\r\n]+)?=', 'onresize([ \t\r\n]+)?=', 'onresizeend([ \t\r\n]+)?=', 'onresizestart([ \t\r\n]+)?=', 'onrowenter([ \t\r\n]+)?=', 'onrowexit([ \t\r\n]+)?=', 'onrowsdelete([ \t\r\n]+)?=', 'onrowsinserted([ \t\r\n]+)?=', 'onscroll([ \t\r\n]+)?=', 'onselect([ \t\r\n]+)?=', 'onselectionchange([ \t\r\n]+)?=', 'onselectstart([ \t\r\n]+)?=', 'onstart([ \t\r\n]+)?=', 'onstop([ \t\r\n]+)?=', 'onsubmit([ \t\r\n]+)?=', 'onunload([ \t\r\n]+)?=');
	   $ra = array_merge($ra1, $ra2);
	   
		foreach ($ra as $tag)
		{
			$pattern = '#'.$tag.'#i';
			preg_match_all($pattern, $val, $matches);
			
			foreach ($matches[0] as $match)
				$val = str_replace($match, substr($match, 0, 2).'-'.substr($match, 2), $val);
		}
		
		return $val;
	}
	
	public static function joinStrip ($data) {
		for ($i= 0 ; $i < count($data) ; $i++) {
			$newInput[]= checkHack::strip($data[$i]);
		}
		$input = implode('_value_',$newInput);
		$stripSpace = preg_replace('/\s/','_SPACE_',$input);
		$stripDot = preg_replace('/\./','_DOT_',$stripSpace);
		$stripSlash = preg_replace('/\./','_SLASH_',$stripDot);
		return base64_encode($stripSpace);
	}

}
?>
