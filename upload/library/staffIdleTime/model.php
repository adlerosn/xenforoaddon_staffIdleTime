<?php

class staffIdleTime_model extends XenForo_Model{
	public function getSiropuLastActivity($uid){
		$data = $this->_getDb()->fetchRow(
		'SELECT user_last_activity AS t FROM xf_siropu_chat_sessions WHERE user_id = ?'
		,$uid);
		return $data['t'];
	}
}
