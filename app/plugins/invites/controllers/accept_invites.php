<?php
class AcceptInvitesController extends InvitesAppController {
	var $uses = array('User');
	var $autoRender = false;
	
	public function index(){
		echo __METHOD__;
	}
	 
