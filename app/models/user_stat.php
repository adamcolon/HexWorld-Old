<?php
class UserStat extends AppModel{
	var $name = 'UserStat';
	var $belongsTo = array('User');

	function reIndex($results){
		$new_results = array();
		foreach ($results as $item){
			$new_results[$item['name']] = $item;
		}
		$results = $new_results;
		
		return $results;
	}
	
	function setStat($user_id, $stat_name, $amount){
		$result = false;
		
		if($stat = $this->find('first', array('conditions'=>array('UserStat.user_id'=>$user_id, 'UserStat.name'=>$stat_name)))){
			$this->id = $stat['UserStat']['id'];
			if($this->saveField('value', $amount)){
				$result = true;
			}
		}
		
		return $result;
	}

	function lowerStat($user_id, $stat_name, $amount=1, $allow_negative = false){
		$result = false;
		
		if($stat = $this->find('first', array('conditions'=>array('UserStat.user_id'=>$user_id, 'UserStat.name'=>$stat_name)))){
			if($allow_negative || $stat['UserStat']['value']>=$amount){
				$this->id = $stat['UserStat']['id'];
				if($this->saveField('value', $stat['UserStat']['value'] - $amount)){
					$result = true;
				}
			}
		}
		
		return $result;
	}
	
	function raiseStat($user_id, $stat_name, $amount=1){
		$result = false;
		
		if($stat = $this->find('first', array('conditions'=>array('UserStat.user_id'=>$user_id, 'UserStat.name'=>$stat_name)))){
			$this->id = $stat['UserStat']['id'];
			if($this->saveField('value', $stat['UserStat']['value'] + $amount)){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function autoIncreaseStats($user){
		$stats = $this->find('all', array('conditions'=>array('UserStat.user_id'=>$user['User']['id'], 'UserStat.auto_increase_allow'=>1)));
		foreach($stats as $stat){
			if($cycles_missed = floor((time()-$stat['UserStat']['auto_increase_last'])/$stat['UserStat']['auto_increase_frequency'])){
				if ($stat['UserStat']['value'] < $user['UserStat']['spaces_owned']['value']){
					$new_value = min(floor($stat['UserStat']['value'] + ($stat['UserStat']['auto_increase_amount'] * $cycles_missed)), max($user['UserStat']['spaces_owned']['value'],$stat['UserStat']['auto_increase_min_value']));
					$update['UserStat'] = array('id'=>$stat['UserStat']['id'], 'value'=>$new_value, 'auto_increase_last'=>time());
					
					$this->log("Update Stat:{$cycles_missed}".print_r($update, true), 'update_stats');
					if(!$this->save($update)){
						$this->log('[UserStat::autoIncreaseStats] Failed To Update Stat', 'debug');
					}
				}
			}
		}
	}

	function selectTile($user_id, $tile_id){
		if($stat = $this->find('first', array('conditions'=>array('UserStat.user_id'=>$user_id, 'UserStat.name'=>'selected_tile')))){
			$this->id = $stat['UserStat']['id'];
			$success = $this->saveField('value', $tile_id);
		}else{
			$update = array('UserStat'=>array('user_id'=>$user_id,'name'=>'selected_tile', 'value'=>$tile_id));
			$success = $this->save($update);
		}
		return $success;
	}
	
}
?>