<?php
class Space extends AppModel{
	var $name = 'Space';
	var $belongsTo = array('User','Tile');
	var $actsAs = array('Containable');
	
	function claimSpace($space_id, $user_id){
		$success = false;
		
		$this->id = $space_id;
		if ($space = $this->read()){
			if($old_user_id = $space['User']['id']){
				$this->User->UserStat->lowerStat($old_user_id, 'score');
				$this->User->UserStat->lowerStat($old_user_id, 'spaces_owned');
			}
			
			if ($this->saveField('user_id', $user_id)){
				$this->User->UserStat->raiseStat($user_id, 'score');
				$this->User->UserStat->raiseStat($user_id, 'spaces_owned');
				$success = true;
			}
		}
		return $success;
	}
	
	function setUnits($space_id, $amount){
		$success = false;
		
		$this->id = $space_id;
		if ($space = $this->read()){
			$new_amount = $amount;
			if ($this->saveField('units', $new_amount)){
				$success = true;
			}
		}
		return $success;
	}

	function raiseUnits($space_id, $amount=1){
		$success = false;
		
		$this->id = $space_id;
		if ($space = $this->read()){
			$new_amount = $space['Space']['units'] + $amount;
			if ($this->saveField('units', $new_amount)){
				$success = true;
			}
		}
		return $success;
	}
	
	function lowerUnits($space_id, $amount=1){
		$success = false;
		
		$this->id = $space_id;
		if ($space = $this->read()){
			if($new_amount = $space['Space']['units'] - $amount){
				if ($this->saveField('units', $new_amount)){
					$success = true;
				}
			}
		}
		return $success;
	}
	
	function changeTile($space_id, $tile_id){
		$this->id = $space_id;
		$space = $this->read();
		
		if($space['Tile']['id'] == 1 && $tile_id != 1){
			$this->User->UserStat->raiseStat($space['User']['id'], 'score');
		}
		
		return $this->saveField('tile_id', $tile_id);
	}
}
?>