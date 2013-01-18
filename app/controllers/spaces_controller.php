<?php
class SpacesController extends AppController {
	var $name = 'Spaces';
	var $helpers = array('Html');
	
	function view($space_id, $details_only=false){
		$space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$space_id), 'contain'=>array('Tile.Resource', 'User')));
		$this->set('space', $space);
		
		if($details_only){
			$this->render('/elements/spaces/details');
			return;
		}
	}
	
	function gameBoard(){
		$contextual_buttons = array(
#			'build'=>array('Choose Tile'=>"jQuery.lightbox('/tilegame/tiles/', {width:600, height:400});")
		);
		
		$this->set('contextual_buttons', $contextual_buttons);
	}

	function gridBoard(){
	}
	
	function getCurrentLocation(){
		$this->autoRender = false;

		$tileSpec['width'] = $this->tileSpec['width'];
		$tileSpec['height'] = $this->tileSpec['height'];

		$sector = 1;
		$x = 0;
		$y = 0;
		if(!empty($this->me['UserLocation'])){
			$sector = $this->me['UserLocation']['sector_id'];
			$x = $this->me['UserLocation']['space_x'];
			$y = $this->me['UserLocation']['space_y'];
		}
		
		$space = $this->Space->find('first', array('conditions'=>array('Space.sector_id'=>$sector, 'Space.x'=>$x, 'Space.y'=>$y)));

		if ($this->params['isAjax']){
			$response = array('dataMap'=>$space, 'tileSpec'=>$tileSpec);
			$data_encoded = json_encode($response);
			echo $data_encoded;
		}else{
			echo "##{$sector}|{$x}|{$y}##";
			$response = array('dataMap'=>$space, 'tileSpec'=>$tileSpec);
			pr($this->me);
			pr($response);
		}
	}
	
	function getDataMap($window_width_coord, $window_height_coord){
		$this->autoRender = false;
		$data_map = array();
		
		$tileSpec['width'] = $this->tileSpec['width'];
		$tileSpec['height'] = $this->tileSpec['height'];

		$window_width = floor($window_width_coord/$tileSpec['width']);
		$window_height = floor($window_height_coord/($tileSpec['height']*3/4));
		
		// Initialize Location
		$sector = 1;
		$x = floor($window_width/2);
		$y = floor($window_height/2);
		if(!empty($this->me['UserLocation'])){
			$sector = $this->me['UserLocation']['sector_id'];
			$x = $this->me['UserLocation']['space_x'];
			$y = $this->me['UserLocation']['space_y'];
		}
		
		$left = $x - floor($window_width/2);
		$right = $x + ceil($window_width/2);
		$top = $y - floor($window_height/2);
		$bottom = $y + ceil($window_height/2);

		if($spaces = $this->Space->find('all', array('conditions'=>array('Space.sector_id'=>$sector, 'Space.x >='=>$left, 'Space.x <='=>$right, 'Space.y >='=>$top, 'Space.y <='=>$bottom), 'order'=>array('Space.y ASC', 'Space.x ASC')))){
			foreach($spaces as $space){
				$position = $this->_getPosition($left, $top, $space['Space']['x'], $space['Space']['y']);
				$data_map[$position['x']][$position['y']] = array(
					'Space'=>$space['Space']
					,'Tile'=>$space['Tile']
					,'User'=>$space['User']
					,'Highlight'=>$this->_getHighlightInfo($space)
					,'Position'=>$position
				);
			}
		}

		if ($this->params['isAjax']){
			$response = array('dataMap'=>$data_map, 'tileSpec'=>$tileSpec);
			$data_encoded = json_encode($response);
			echo $data_encoded;
		}else{
			$response = array('dataMap'=>$data_map, 'tileSpec'=>$tileSpec);

			echo "[WWC:{$window_width_coord}|WHC:{$window_height_coord}|WW:{$window_width}|WH:{$window_height}|L:{$left}|R:{$right}|T:{$top}|B:{$bottom}|x:{$x}|y:{$y}]";
			pr($this->me);
			pr($response);
		}
	}
	
	function attack($source_id, $target_id){
		$result['success'] = 0;
		$messages = array();
		
		if ($this->me['UserStat']['attack_points']){
			$source_space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$source_id)));
			$target_space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$target_id)));
			
			if ($source_space && $target_space){
				if (!empty($source_space['User']) && $source_space['User']['id'] == $this->me['User']['id']){
					if (empty($target_space['User']) || $target_space['User']['id'] != $this->me['User']['id']){
						if(!empty($source_space['Space']['units']) && $source_space['Space']['units'] > 1){
							if($this->Space->User->UserStat->lowerStat($this->me['User']['id'], 'attack_points', 1)){
								$bonuses = $this->_getConflictBonuses($source_space, $target_space);
								$attacks = $source_space['Space']['units'];
								for($i=1;$i<=$attacks;$i++){
									if($source_space['Space']['units'] <= 1 || $target_space['Space']['units'] == 0){
										break;
									}
									$attack_score = $bonuses['attack'] + rand(1,100);
									$defense_score = $bonuses['defense'] + rand(1,100);
									if ($attack_score > $defense_score){
										$target_space['Space']['units']--;
									}else{
										$source_space['Space']['units']--;
									}
									$result['scoreboard'][] = array('attack_score'=>$attack_score, 'defense_score'=>$defense_score);
								}

								if($target_space['Space']['units'] == 0){
									$messages[] = 'You Won!';
									$this->Space->User->UserStat->lowerStat($target_space['User']['id'], 'score');
									
									// Change Ownership
									if (!$this->Space->claimSpace($target_space['Space']['id'], $this->me['User']['id'])){
										$this->log('failed to claim ownership', 'debug');
									}

									// Deploy Units
									$this->Space->setUnits($target_space['Space']['id'], $source_space['Space']['units']-1);
									$this->Space->setUnits($source_space['Space']['id'], 1);
								}else{
									$message[] = 'You Lost!';
									
									// Update Units
									$this->Space->setUnits($target_space['Space']['id'], $target_space['Space']['units']);
									$this->Space->setUnits($source_space['Space']['id'], $source_space['Space']['units']);

								}
							}else{
								$messages[] = 'Failed to lower your Attack Points';
							}
						}else{
							$messages[] = 'Not Enough Units to Attack...';
						}
					}else{
						$messages[] = "You invade yourself and win...";
					}
				}else{
					$messages[] = "You don't own this";
				}
			}else{
				$messages[] = "Not sure what you're trying to do";
			}
		}else{
			$messages[] = 'No more attack points';
		}
		
		$result['messages'] = $messages;
		$result['units'] = array('target'=>$target_space['Space']['units'], 'source'=>$source_space['Space']['units']);
		
		$this->autoRender = false;
		echo json_encode($result);
	}

	function deploy($target_id, $quantity=1){
		$result['success'] = 0;
		$messages = array();
		
		if ($this->me['UserStat']['reserve_units']['value']){
			if($space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$target_id)))){
				if	((empty($space['Space']['user_id']) && $this->me['UserStat']['spaces_owned']['value'] == 0)
				|| (!empty($space['Space']['user_id']) && $space['User']['id'] == $this->me['User']['id'])){
					if($this->Space->User->UserStat->lowerStat($this->me['User']['id'], 'reserve_units')){
						// If Space has no owner and User has no spaces owned, then take the space
						if(empty($space['User']['id'])){
							$this->Space->claimSpace($space['Space']['id'], $this->me['User']['id']);
							if($this->Space->raiseUnits($space['Space']['id'], $quantity)){
								$result['success'] = 1;
							}
						}else{
							if($this->Space->raiseUnits($space['Space']['id'], $quantity)){
								$result['success'] = 1;
							}
						}
					}else{
						$messages[] = 'Failed to deduct reserve units';
						$this->log("Failed to Update reserve_units",'debug');
					}
				}else{
					$messages[] = 'Not Deployable due to ownership';
					$this->log("Not Deployable due to ownership",'debug');
				}
			}else{
				$messages[] = 'Could Not Find Space';
				$this->log("Could Not Find Space",'debug');
			}
		}else{
			$messages[] = 'No Reserve Units';
			$this->log("No Reserve Units",'debug');
		}
		
		$result['messages'] = $messages;
		$this->autoRender = false;
		echo json_encode($result);
	}
	
	function explore($space_id){
		$messages = array();
		$explore_radius = 1;

		if($space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$space_id)))){
			if ($this->me['UserStat']['explore_points']){
				if($space['User']['id'] == $this->me['User']['id'] || $this->me['UserStat']['spaces_owned']['value'] == 0){
					for($i=0;$i<=$explore_radius;$i++){
						$x_list[] = $space['Space']['x']+$i;
						$x_list[] = $space['Space']['x']-$i;
						
						$y_list[] = $space['Space']['y']+$i;
						$y_list[] = $space['Space']['y']-$i;
					}
					
					$empty_neighbors = array();
					foreach ($x_list as $x){
						foreach($y_list as $y){
							if(!$neighbor_space = $this->Space->find('first', array('conditions'=>array('Space.x'=>$x, 'Space.y'=>$y)))){
								$empty_neighbors[] = array('x'=>$x, 'y'=>$y);
							}
						}
					}
					
					if($empty_neighbors){
						$selected = $empty_neighbors[rand(0,count($empty_neighbors)-1)];
						
						$tile = $this->Space->Tile->getRandomTile();
						
						$new_space['Space'] = array('tile_id'=>$tile['Tile']['id'], 'x'=>$selected['x'], 'y'=>$selected['y']);
						if($this->Space->User->UserStat->lowerStat($this->me['User']['id'], 'explore_points')){
							if($this->Space->save($new_space)){
								$new_space_id = $this->Space->id;
								if($this->Space->User->UserStat->lowerStat($this->me['User']['id'], 'reserve_units')){
									if($this->Space->claimSpace($new_space_id, $this->me['User']['id'])){
										if($this->Space->raiseUnits($new_space_id)){
											$result['success'] = 1;
										}else{
											$messages[] = "Failed to add units to space.";
										}
									}else{
										$messages[] = 'Failed to claim space.';
									}
								}else{
									$messages[] = 'Failed to deduct reserve units';
								}
							}else{
								$messages[] = 'Failed to save new space.';
							}
						}else{
							$messages[] = 'Failed to deduct exploration points';
						}
					}else{
						$messages[] = 'Nowhere to explore.';
					}
				}else{
					$messages[] = "You don't own this space.";
				}
			}else{
				$messages[] = 'No Exploring Points or Reserve Units';
			}
		}else{
			$messages[] = 'Could not find space.';
		}

		if($messages) $this->log(print_r($messages, true), 'debug');
		
		$result['messages'] = $messages;
		$this->autoRender = false;
		echo json_encode($result);
	}

	function build($space_id, $tile_id){
		$result['success'] = 0;
		$messages = array();

		if($tile = $this->Space->Tile->find('first', array('conditions'=>array('Tile.id'=>$tile_id, 'Tile.deleted'=>null)))){
			if($this->me['UserStat']['reserve_units']['value'] >= $tile['Tile']['cost']){
				if($space = $this->Space->find('first', array('conditions'=>array('Space.id'=>$space_id)))){
					if	((!empty($space['Space']['user_id']) && $space['User']['id'] == $this->me['User']['id'])){
						if($this->Space->User->UserStat->lowerStat($this->me['User']['id'], 'reserve_units', $tile['Tile']['cost'])){
							// If Space has no owner and User has no spaces owned, then take the space
							if($this->Space->changeTile($space['Space']['id'], $tile_id)){
								$result['success'] = 1;
								$this->log("Successfully Changed [space:{$space['Space']['id']}, tile:{$tile_id}]", 'debug');
							}
						}else{
							$messages[] = 'Failed to deduct reserve units';
							$this->log("Failed to Update reserve_units",'debug');
						}
					}else{
						$messages[] = 'Not buildable due to ownership';
						$this->log("Not buildable due to ownership",'debug');
					}
				}else{
					$messages[] = 'Could Not Find Space';
					$this->log("Could Not Find Space",'debug');
				}
			}else{
				$messages[] = 'Not enough Reserve Units';
				$this->log("Not enough Reserve Units",'debug');
			}
		}else{
			$messages[] = 'No Tile Found';
			$this->log("No Tile Found",'debug');
		}
		
		$result['messages'] = $messages;
		$this->autoRender = false;
		echo json_encode($result);
	}
	
	function _getConflictBonuses($source_space, $target_space){
		// Terrain Bonus
		$response['attack'] = $source_space['Tile']['attack_modifier'];
		$respones['defense'] = $target_space['Tile']['defense_modifier'];
		
		return $response;
	}

	function _getPosition($board_left,$board_top, $x, $y){
		$position['x'] = $x - $board_left;
		$position['y'] = $y - $board_top;
		$position['width'] = $this->tileSpec['width']*$this->me['User']['tile_size'];
		$position['height'] = $this->tileSpec['height']*$this->me['User']['tile_size'];
		$position['left'] = $position['width'] * $position['x'];
		if($y%2) $position['left'] += floor($position['width']/2);
		
		$position['top'] = (floor(($position['height']*3)/4) * $position['y']);
		$position['center'] = array('x'=>($position['left'] + floor($position['width']/2)), 'y'=>($position['top'] + floor($position['height']/2)));
		
		return $position;
	}

	function _getHighlightInfo($space){
		$color = '#fc7f5d';
		if(!empty($space['User']['color'])){
			$color = $space['User']['color'];
		}
		return array('color'=>$color);
	}
}
