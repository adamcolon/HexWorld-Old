<?php
class Sector extends AppModel{
	var $name = 'Sector';
	var $hasMany = array('Space');
}
?>