<?php
class UsersController extends AppController {
	var $name = 'Users';
	var $autoRender = false;

	function edit(){
		$errors = array();
		$this->_reloadUser();
		
		if(empty($this->params['url']['update'])){
			$this->log(print_r($this->params, true), 'debug');
			$this->render('edit');
		}else{
			$this->User->id = $this->me['User']['id'];
			if(!empty($this->params['url']['name'])) $this->User->set('name', $this->params['url']['name']);
			if(!empty($this->params['url']['color'])) $this->User->set('color', $this->params['url']['color']);
			if(!empty($this->params['url']['tile_size'])) $this->User->set('tile_size', $this->params['url']['tile_size']);
			if($this->User->save()){
				$this->log('Saved', 'debug');
				echo json_encode(array('success'=>1));
			}else{
				$this->log('Not Saved', 'debug');
				echo json_encode(array('success'=>1));
			}
		}
	}
	
	function getHeader(){
		$this->me = $this->User->find('first', array('conditions'=>array('User.id'=>$this->me['User']['id'])));
		$this->set('me', $this->me);
		$this->render('/elements/header');
	}

	function getMe(){
		$this->me = $this->User->find('first', array('conditions'=>array('User.id'=>$this->me['User']['id'])));
		$this->set('me', $this->me);
		
		if($this->params['isAjax']){
			echo json_encode($this->me);
		}else{
			pr($this->me);
		}
	}
	
	function positionChange($sector, $space_x, $space_y, $location_x=null, $location_y=null){
		$errors = array();
		
		$update = array(
			'sector_id'=>$sector
			,'space_x'=>$space_x
			,'space_y'=>$space_y
			,'location_x'=>$location_x
			,'location_y'=>$location_y
		);
		
		if($location = $this->User->UserLocation->find('first', array('conditions'=>array('UserLocation.user_id'=>$this->me['User']['id'])))){
			$update['id'] = $location['UserLocation']['id'];
		}
		
		if(!$this->User->UserLocation->save($update)){
			$errors[] = 'Failed to Save';
		}
		
		return empty($errors);
	}

	function positionChangeCoords($operation, $amount_x, $amount_y){
		$success = false;
		
		//Convet to tile space
		$amount_x = floor($amount_x / $this->tileSpec['width']);
		$amount_x = floor($amount_y / $this->tileSpec['height']);
		
		return $this->positionChange($operation, $amount_x, $amount_y);
	}

	function selectedTile($tile_id=null){
		$response = false;
		
		if (!empty($tile_id)){
			if(!$this->User->UserStat->selectTile($this->me['User']['id'], $tile_id)){
				$this->log("[UsersController::selectedTile] Select Tile Set Failed [tile_id:{$tile_id}]",'debug');
			}
			$this->_reloadUser();
		}

		$this->loadModel('Tile');
		if($tile = $this->Tile->find('first', array('conditions'=>array('Tile.id'=>$this->me['UserStat']['selected_tile']['value']),'contain'=>array()))){
			$response = json_encode($tile);
		}
		
		return $response;
	}
}

?>