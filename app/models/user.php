<?php
class User extends AppModel{
	var $name = 'User';
	var $hasOne = array('UserFacebook','UserLocation');
	var $hasMany = array('UserStat','UserResource');
	
	var $actsAs = array('Containable');
	
	function afterFind($results, $primary){
		if (empty($results['id'])){
			foreach ($results as $key=>$user){
				if (!empty($user['UserStat'])){
					$results[$key]['UserStat'] = $this->UserStat->reIndex($user['UserStat']);
				}

				if (!empty($user['UserResource'])){
					$results[$key]['UserResource'] = $this->UserResource->reIndex($user['UserResource']);
				}
			}
		}
		return $results;
	}
	
	function addNewUser($fb_user){
		$success = false;
		
		$user_info['User'] = array(
			'name'=>$fb_user['first_name']
			,'color'=>$this->randomColor()
		);
		
		$user_info['UserFacebook'] = array(
			'id'=>$fb_user['id']
			,'name'=>$fb_user['name']
			,'first_name'=>$fb_user['first_name']
			,'last_name'=>$fb_user['last_name']
			,'gender'=>$fb_user['gender']
			,'timezone'=>$fb_user['timezone']
			,'locale'=>$fb_user['locale']
			,'updated_time'=>$fb_user['updated_time']
		);

		if(!empty($fb_user['verified'])) $user_info['UserFacebook']['verified'] = $fb_user['verified'];
		
		$user_info['UserLocation'] = array(
			'sector' => 1
			,'space_x' => 0
			,'space_y' => 0
		);
		
		if($this->saveAll($user_info)){
			$user_id = $this->id;
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'score', 'value'=>0);
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'position-x', 'value'=>0);
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'position-y', 'value'=>0);
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'spaces_owned', 'value'=>0);
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'reward_points', 'value'=>5);
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'reserve_units', 'value'=>5, 'auto_increase_allow'=>1, 'auto_increase_amount'=>1, 'auto_increase_frequency'=>180, 'auto_increase_last'=>time());
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'attack_points', 'value'=>5, 'auto_increase_allow'=>1, 'auto_increase_amount'=>1, 'auto_increase_frequency'=>180, 'auto_increase_last'=>time());
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'explore_points', 'value'=>5, 'auto_increase_allow'=>1, 'auto_increase_amount'=>1, 'auto_increase_frequency'=>180, 'auto_increase_last'=>time());
			$user_stats[]['UserStat'] = array('user_id'=>$user_id, 'name'=>'build_points', 'value'=>5, 'auto_increase_allow'=>1, 'auto_increase_amount'=>1, 'auto_increase_frequency'=>180, 'auto_increase_last'=>time());
			
			if($this->UserStat->saveAll($user_stats)){
				$success = true;
			}else{
				$this->log("Error Creating User Stats [{$user_id}]", 'debug');
			}
		}else{
			$this->log("Error Creating User [{$fb_user['id']}]", 'debug');
		}
		
		return $success;
	}
	
	function randomColor(){
		$red = sprintf('%02s', dechex(rand(0,255)));
		$green = sprintf('%02s', dechex(rand(0,255)));
		$blue = sprintf('%02s', dechex(rand(0,255)));
		
		return "#{$red}{$green}{$blue}";
	}
}
?>