<?php
class UserResource extends AppModel{
	var $name = 'UserResource';
	var $belongsTo = array('User','Resource');
	
	var $actsAs = array('Containable');
	var $recursive = 2;
	
	function reIndex($results){
		$new_results = array();
		foreach ($results as $item){
			$new_results[$item['resource_id']] = $item;
		}
		$results = $new_results;
		
		return $results;
	}
	
	function lowerResource($user_id, $resource_id, $amount=1){
		$result = false;
		
		if($stat = $this->find('first', array('conditions'=>array('UserResource.user_id'=>$user_id, 'UserResource.resource_id'=>$resource_id)))){
			if(!empty($stat['UserResource']['value'])){
				$this->id = $stat['UserResource']['id'];
				$save['UserResource'] = array('value'=> $stat['UserResource']['value'] - $amount);
			}else{
				$save['UserResource'] = array(
					'user_id' => $user_id
					,'resource_id' => $resource_id
					,'value' => 0
				);
			}

			if($this->save($save)){
				$result = true;
			}
		}
		
		return $result;
	}
	
	function raiseResource($user_id, $resource_id, $amount=1){
		$result = false;
		
		if($stat = $this->find('first', array('conditions'=>array('UserResource.user_id'=>$user_id, 'UserResource.resource_id'=>$resource_id)))){
			if(!empty($stat['UserResource']['value'])){
				$this->id = $stat['UserResource']['id'];
				$save['UserResource'] = array('value'=> $stat['UserResource']['value'] + $amount);
			}else{
				$save['UserResource'] = array(
					'user_id' => $user_id
					,'resource_id' => $resource_id
					,'value' => $amount
				);
			}

			if($this->save($save)){
				$result = true;
			}
		}
		
		return $result;
	}
	
}
?>