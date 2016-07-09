<?php
class staffIdleTime_controller extends XenForo_ControllerAdmin_Abstract
{
	public function actionIndex(){
		$userModel = $this->getModelFromCache('XenForo_Model_User');
		$users = $userModel->getUsers(array('is_staff' => true), array(
				'join' => XenForo_Model_User::FETCH_USER_FULL | XenForo_Model_User::FETCH_LAST_ACTIVITY,
				'order' => 'user_id'
		));
		
		//die(print_r($users,true));
		
		$staffs = array();
		function cmp_by_customField($a, $b) {
			return $a['inactivity_seconds'] - $b['inactivity_seconds'];
		}
		foreach($users AS $user){
			$staff = array();
			$staff['user_id'] = $user['user_id'];
			$staff['username'] = $user['username'];
			$staff['last_activity'] = $user['effective_last_activity'];
			$staff['last_activity_fmt'] = strftime('%Y-%m-%d %H:%M:%S',$staff['last_activity']);
			$staff['inactivity_seconds'] = time()-$user['effective_last_activity'];
			$staff['inactivity_minutes'] = number_format($staff['inactivity_seconds']/(60),2);
			$staff['inactivity_hours']   = number_format($staff['inactivity_seconds']/(3600),2);
			$staff['inactivity_days']    = number_format($staff['inactivity_seconds']/(24*3600),2);
			$staff['inactivity_weeks']   = number_format($staff['inactivity_seconds']/(7*24*3600),2);
			$staff['inactivity_months']  = number_format($staff['inactivity_seconds']/(30*24*3600),2);
			
			$staffs[]=$staff;
		}
		usort($staffs, 'cmp_by_customField');
		
		$downloadmode = $this->_input->filterSingle('downloadmode',XenForo_Input::STRING);
		if($downloadmode){
			if($downloadmode == 'json'){
				$downloadable = json_encode($staffs);
				$fsize = strlen($downloadable);
				$fname = 'staff_idle_time_-_'.date('Y-m-d--G-i-s--e',time()).'.json';
				$mime = 'application/json';
				header('Content-Type: '.$mime);
				header('Content-Disposition: attachment; filename="'.$fname.'"');
				header('Content-Length: ' . $fsize);
				header('Connection: close');
				die($downloadable);
			}
		}
		
		//die(print_r($staffs,true));
		
		$viewParams = array('staffs'=>$staffs);
		return $this->responseView(
            'XenForo_ViewAdmin_Base',
            'kiror_staff_idle_time',
            $viewParams
        );
	}
}
