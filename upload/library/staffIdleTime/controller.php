<?php
class staffIdleTime_controller extends XenForo_ControllerAdmin_Abstract{
	public function actionIndex(){
		$userModel = $this->getModelFromCache('XenForo_Model_User');
		$users = $userModel->getUsers(array('is_staff' => true), array(
				'join' => XenForo_Model_User::FETCH_LAST_ACTIVITY,
				'order' => 'user_id'
		));
		//staffIdleTime_DownloadHelper::sendAsDownload(json_encode($users,JSON_PRETTY_PRINT),'file.json','application/json',false);
		
		//die(print_r($users,true));
		
		$staffs = array();
		$staffids = array();
		function cmp_by_customField($a, $b) {
			return $a['user_id'] - $b['user_id'];
		}
		
		$isSiropuChatInstalled = $this->_isAddOnIdInstalledAndActivated('siropu_chat');
		//$isSiropuChatInstalled = false;
		
		$contexts=['forum','account'];
		if($isSiropuChatInstalled) $contexts[]='chat';
		
		foreach($users AS $user){
			$staff = array();
			$staff['user_id'] = $user['user_id'];
			$staff['username'] = $user['username'];
			$staff['last_activity'] = [];
			$staff['last_activity']['forum'] = [];
			$staff['last_activity']['forum']['val'] = $user['last_activity'];
			$staff['last_activity']['forum']['fmt'] = strftime('%Y-%m-%d %H:%M:%S',$staff['last_activity']['forum']['val']);
			$staff['last_activity']['account'] = [];
			$staff['last_activity']['account']['val'] = $user['effective_last_activity'];
			$staff['last_activity']['account']['fmt'] = strftime('%Y-%m-%d %H:%M:%S',$staff['last_activity']['account']['val']);
			if($isSiropuChatInstalled){
				$staff['last_activity']['chat'] = [];
				$staff['last_activity']['chat']['val'] = ($this->_getThisModel()->getSiropuLastActivity($staff['user_id']));
				$staff['last_activity']['chat']['val'] = intval($staff['last_activity']['chat']['val']);
				$staff['last_activity']['chat']['fmt'] = strftime('%Y-%m-%d %H:%M:%S',$staff['last_activity']['chat']['val']);
			}
			$staffs[]=$staff;
			$staffids[]=$user['user_id'];
		}
		usort($staffs, 'cmp_by_customField');
		
		$downloadmode = $this->_input->filterSingle('downloadmode',XenForo_Input::STRING);
		if($downloadmode){
			if($downloadmode == 'json'){
				$downloadable = json_encode($staffs,JSON_PRETTY_PRINT);
				$fname = 'staff_idle_time_-_'.date('Y-m-d--G-i-s--e',time()).'.json';
				$mime = 'application/json';
				staffIdleTime_DownloadHelper::sendAsDownload($downloadable,$fname,$mime,true);
			}
		}
		
		//staffIdleTime_DownloadHelper::sendAsDownload(json_encode($users,JSON_PRETTY_PRINT),'file.json','application/json',false);
		//staffIdleTime_DownloadHelper::sendAsDownload(json_encode($staffs,JSON_PRETTY_PRINT),'file.json','application/json',false);
		
		$viewParams = array('staffs'=>$staffs,'staffids'=>$staffids,'contexts'=>$contexts);
		return $this->responseView(
            'XenForo_ViewAdmin_Base',
            'kiror_staff_idle_time',
            $viewParams
        );
	}
	/**
	 * Check if some add-on is installed and activated.
	 * 
	 * @param addOnId string
	 * @return boolean
	 */
	protected function _isAddOnIdInstalledAndActivated($addOnId){
		$addon = $this->_getAddOnModel()->getAddOnById($addOnId);
		return (is_array($addon) && isset($addon['active']) && $addon['active']);
	}
	/**
	 * Gets the add-on model object.
	 *
	 * @return XenForo_Model_AddOn
	 */
	protected function _getAddOnModel(){
		return $this->getModelFromCache('XenForo_Model_AddOn');
	}
	/**
	 * Gets the model object of this addon.
	 *
	 * @return XenForo_Model_AddOn
	 */
	protected function _getThisModel(){
		return $this->getModelFromCache('staffIdleTime_model');
	}
}
