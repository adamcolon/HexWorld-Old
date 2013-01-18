<?php
class Tile extends AppModel{
	var $name = 'Tile';
	var $belongsTo = array('Resource'=>array('foreignKey'=>'production_resource_id'));
	var $hasMany = array('Space');
	var $hasAndBelongsToMany = array(
		'ResourceCost'=>array(
			'className' => 'Resource'
			,'joinTable' => 'tile_costs'
		)
	);
	
	var $actsAs = array('Containable');
	
	function getRandomTile(){
		$response = false;
		if($tiles = $this->find('all', array('conditions'=>array('Tile.deleted'=>null)))){
			$total = 0;
			foreach ($tiles as $tile){
				$total += $tile['Tile']['cost'];
			}

			$tile_list = array();
			foreach ($tiles as $tile){
				for($i=1;$i<=floor($total/$tile['Tile']['cost']);$i++){
					$tile_list[] = $tile;
				}
			}
			
			$response = $tile_list[rand(0, count($tile_list)-1)];
		}
		
		return $response;
	}
}
?>