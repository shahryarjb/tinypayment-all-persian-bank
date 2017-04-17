<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 20016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access to this file
defined('_JEXEC') or die('Restricted access');

class config {	
    function loadMainSettings () {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__tinypayment_settings') . 'as settings');
        $query->where($db->qn('settings.id') . ' = 1');
        
        $db->setQuery((string)$query); 
        $result = $db->loadObject();
        return $result;
    }

    function loadPortSettings ($bank_id) {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select('*');
        $query->from($db->qn('#__tinypayment_banks') . 'as ps');
        $query->where($db->qn('ps.bank_id') . ' = ' . $db->q($bank_id));
        
        $db->setQuery((string)$query); 
        $result = $db->loadObject();
        return $result;
    }

    public function checkUpdate(){
        $url = "https://trangell.com/ext/updates/component/com_tinypayment.xml";
        JLoader::import('joomla.updater.update');

        $update = new JUpdate();
        $update->loadFromXML($url);
        $newVersion = $update->get('version')->_data;

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);

        $query->select($db->quoteName(array('name', 'manifest_cache')))
        ->from($db->quoteName('#__extensions'))
        ->where($db->qn('type') . ' = ' . $db->q('component'))
        ->where($db->qn('element') . ' = ' . $db->q('com_tinypayment'));
        $db->setQuery($query);

        $results = $db->loadObjectList();

        foreach ($results as $extension) {
            $decode = json_decode($extension->manifest_cache);
            $oldVersion =  $decode->version;
        }
        if ($newVersion > $oldVersion ){
           $alert  = '<div class="alert ">';
                $alert .= '<p><h2>مدیریت محترم :</h2></p>
                <p><a title="کامپوننت آسان پرداخت جامع جوملا" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا">کامپوننت آسان پرداخت جامع جوملا</a> دارای نسخه جدید می باشد. این نسخه می تواند شامل اضافه شدن امکانات جدید یا حل مشکلات گزارش شده می باشد . به همین دلیل برای امنیت بهتر و دریافت امکانات مناسب تر از این کامپوننت کاربردی به صفحه <a title="کامپوننت آسان پرداخت" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا">کامپوننت آسان پرداخت</a> مراجعه کنید.</p>
                <p>توجه :  در صورتی که نیازمند امکانات اختصاصی برای این افزونه هستید می توانید به صفحه ( <a title="افزونه نویسی جوملا" href="https://trangell.com/fa/blog/6-خدمات-افزونه-نویسی">افزونه نویسی جوملا</a> مراجعه کنید ) </p>';
                $alert .= '</div>';
                echo $alert;
        }
        else {
            return false;
        }
	}

    public function jsonVer($ext) {
        $url = 'https://trangell.com/ext/updates/component/ver.json';
        $ver = "0.1.2";
        $content = file_get_contents($url);
        $json = json_decode($content, true);
        if ($ext == "tinypayment") {
            if ($json['tinypayment'] != $ver) {
                $alert  = '<div class="alert ">';
                $alert .= '<p><h2>مدیریت محترم :</h2></p>
                <p><a title="کامپوننت آسان پرداخت جامع جوملا" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا">کامپوننت آسان پرداخت جامع جوملا</a> دارای نسخه جدید می باشد. این نسخه می تواند شامل اضافه شدن امکانات جدید یا حل مشکلات گزارش شده می باشد . به همین دلیل برای امنیت بهتر و دریافت امکانات مناسب تر از این کامپوننت کاربردی به صفحه <a title="کامپوننت آسان پرداخت" href="https://trangell.com/fa/blog/90-کامپوننت-آسان-پرداخت-جامع-جوملا">کامپوننت آسان پرداخت</a> مراجعه کنید.</p>
                <p>توجه :  در صورتی که نیازمند امکانات اختصاصی برای این افزونه هستید می توانید به صفحه ( <a title="افزونه نویسی جوملا" href="https://trangell.com/fa/blog/6-خدمات-افزونه-نویسی">افزونه نویسی جوملا</a> مراجعه کنید ) </p>';
                $alert .= '</div>';
                echo $alert;
            }
        }
        if ($ext == "tinypaymentver") {
            if ($json['tinypayment'] != $ver) {
                return 1;
            }
        }
    }
}