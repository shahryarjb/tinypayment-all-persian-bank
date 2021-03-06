<?php
/**
 * @package     Joomla - > Site and Administrator payment info
 * @subpackage  com_tinypayment
 * @copyright   trangell team => https://trangell.com
 * @copyright   Copyright (C) 2017 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */
defined('_JEXEC') or die('Restricted access');

class TinyPaymentTableStatistics extends JTable
{

	function __construct(&$db)
	{
		parent::__construct('#__tinypayment_transactions', 'id', $db);
	}

	public function bind($array, $ignore = '')
	{
		if (isset($array['params']) && is_array($array['params']))
		{
			$parameter = new JRegistry;
			$parameter->loadArray($array['params']);
			$array['params'] = (string)$parameter;
		}

		if (isset($array['rules']) && is_array($array['rules']))
		{
			$rules = new JAccessRules($array['rules']);
			$this->setRules($rules);
		}

		return parent::bind($array, $ignore);
	}

	public function load($pk = null, $reset = true)
	{
		if (parent::load($pk, $reset))
		{
			$params = new JRegistry;
			$this->params = $params;
			return true;
		}
		else
		{
			return false;
		}
	}

	protected function _getAssetName()
	{
		$k = $this->_tbl_key;
		return 'com_tinypayment.message.'.(int) $this->$k;
	}

	protected function _getAssetTitle()
	{
		return $this->user_name;
	}

	protected function _getAssetParentId(JTable $table = NULL, $id = NULL)
	{
		$assetParent = JTable::getInstance('Asset');
		$assetParentId = $assetParent->getRootId();

		if ($assetParent->id)
		{
			$assetParentId=$assetParent->id;
		}
		return $assetParentId;
	}
}
