<?php
/**
 * Static content controller.
 *
 * This file will render views from views/pages/
 *
 * PHP versions 4 and 5
 *
 * CakePHP(tm) : Rapid Development Framework (http://cakephp.org)
 * Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 *
 * Licensed under The MIT License
 * Redistributions of files must retain the above copyright notice.
 *
 * @copyright     Copyright 2005-2010, Cake Software Foundation, Inc. (http://cakefoundation.org)
 * @link          http://cakephp.org CakePHP(tm) Project
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 * @since         CakePHP(tm) v 0.2.9
 * @license       MIT License (http://www.opensource.org/licenses/mit-license.php)
 */

/**
 * Static content controller
 *
 * Override this controller by placing a copy in controllers directory of an application
 *
 * @package       cake
 * @subpackage    cake.cake.libs.controller
 */
class TilesController extends AppController {

/**
 * Controller name
 *
 * @var string
 * @access public
 */
	var $name = 'Tiles';

/**
 * Default helper
 *
 * @var array
 * @access public
 */
	var $helpers = array('Html');

/**
 * This controller does not use a model
 *
 * @var array
 * @access public
 */
	var $uses = array();

	function getDataMap(){
		$dataMap[] = array('y'=>0, 'x'=>2, 'image_url'=>'tile.png');
		,{'y':0, 'x':3, 'image_url':'tile.png'}
		,{'y':0, 'x':4, 'image_url':'tile.png'}
		,{'y':0, 'x':5, 'image_url':'tile.png'}
		,{'y':0, 'x':6, 'image_url':'tile.png'}

		,{'y':1, 'x':1, 'image_url':'tile.png'}
		,{'y':1, 'x':2, 'image_url':'tile.png'}
		,{'y':1, 'x':3, 'image_url':'tile.png'}
		,{'y':1, 'x':4, 'image_url':'tile.png'}
		,{'y':1, 'x':5, 'image_url':'tile.png'}
		,{'y':1, 'x':6, 'image_url':'tile.png'}
		
		,{'y':2, 'x':1, 'image_url':'tile.png'}
		,{'y':2, 'x':2, 'image_url':'tile.png'}
		,{'y':2, 'x':3, 'image_url':'tile.png'}
		,{'y':2, 'x':4, 'image_url':'tile.png'}
		,{'y':2, 'x':5, 'image_url':'tile.png'}
		,{'y':2, 'x':6, 'image_url':'tile.png'}
		,{'y':2, 'x':7, 'image_url':'tile.png'}
		
		,{'y':3, 'x':0, 'image_url':'tile.png'}
		,{'y':3, 'x':1, 'image_url':'tile.png'}
		,{'y':3, 'x':2, 'image_url':'tile.png'}
		,{'y':3, 'x':3, 'image_url':'tile.png'}
		,{'y':3, 'x':4, 'image_url':'tile.png'}
		,{'y':3, 'x':5, 'image_url':'tile.png'}
		,{'y':3, 'x':6, 'image_url':'tile.png'}
		,{'y':3, 'x':7, 'image_url':'tile.png'}
		
		,{'y':4, 'x':0, 'image_url':'tile.png'}
		,{'y':4, 'x':1, 'image_url':'tile.png'}
		,{'y':4, 'x':2, 'image_url':'tile.png'}
		,{'y':4, 'x':3, 'image_url':'tile.png'}
		,{'y':4, 'x':4, 'image_url':'tile.png'}
		,{'y':4, 'x':5, 'image_url':'tile.png'}
		,{'y':4, 'x':6, 'image_url':'tile.png'}
		,{'y':4, 'x':7, 'image_url':'tile.png'}
		,{'y':4, 'x':8, 'image_url':'tile.png'}
		
		,{'y':5, 'x':0, 'image_url':'tile.png'}
		,{'y':5, 'x':1, 'image_url':'tile.png'}
		,{'y':5, 'x':2, 'image_url':'tile.png'}
		,{'y':5, 'x':3, 'image_url':'tile.png'}
		,{'y':5, 'x':4, 'image_url':'tile.png'}
		,{'y':5, 'x':5, 'image_url':'tile.png'}
		,{'y':5, 'x':6, 'image_url':'tile.png'}
		,{'y':5, 'x':7, 'image_url':'tile.png'}

		,{'y':6, 'x':1, 'image_url':'tile.png'}
		,{'y':6, 'x':2, 'image_url':'tile.png'}
		,{'y':6, 'x':3, 'image_url':'tile.png'}
		,{'y':6, 'x':4, 'image_url':'tile.png'}
		,{'y':6, 'x':5, 'image_url':'tile.png'}
		,{'y':6, 'x':6, 'image_url':'tile.png'}
		,{'y':6, 'x':7, 'image_url':'tile.png'}

		,{'y':7, 'x':1, 'image_url':'tile.png'}
		,{'y':7, 'x':2, 'image_url':'tile.png'}
		,{'y':7, 'x':3, 'image_url':'tile.png'}
		,{'y':7, 'x':4, 'image_url':'tile.png'}
		,{'y':7, 'x':5, 'image_url':'tile.png'}
		,{'y':7, 'x':6, 'image_url':'tile.png'}

		,{'y':8, 'x':2, 'image_url':'tile.png'}
		,{'y':8, 'x':3, 'image_url':'tile.png'}
		,{'y':8, 'x':4, 'image_url':'tile.png'}
		,{'y':8, 'x':5, 'image_url':'tile.png'}
		,{'y':8, 'x':6, 'image_url':'tile.png'}

	}

}
