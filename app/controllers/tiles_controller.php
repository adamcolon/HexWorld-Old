<?php
class TilesController extends AppController {
	var $name = 'Tiles';

	function index(){
#pr($this->me);
		$tiles = $this->Tile->find('all', array('conditions'=>array('Tile.deleted'=>null, 'Tile.buildable'=>1), 'order'=>'Tile.cost ASC', 'contain'=>array('Resource', 'ResourceCost')));
		$this->set('tiles', $tiles);
	}
	
	function select($space_id){
#pr($this->me);
		$tiles = $this->Tile->find('all', array('conditions'=>array('Tile.deleted'=>null, 'Tile.buildable'=>1), 'order'=>'Tile.cost ASC', 'contain'=>array('Resource', 'ResourceCost')));
		$this->set('tiles', $tiles);
		$this->set('space_id', $space_id);
	}

	function getTiles(){
		$this->autoRender = false;
		
		$tiles = $this->Tile->find('all', array('contain'=>array('Resource', 'ResourceCost')));
		echo json_encode($tiles);
	}
}
?>