<?php
class EmailInvite extends InvitesAppModel {
	var $name = 'EmailInvite';

	public function test(){
		echo __METHOD__;
	}
	
	public function clearToSend($user_id, $email){
		$errors = $actions = array();
		$clear = true;
		
		if($this->validateEmail($email)){
			if($invite = $this->find('first', array('conditions'=>array('EmailInvite.user_id'=>$user_id, 'EmailInvite.email'=>$email)))){
				$clear = false;
				$actions[] = "You already sent {$email} an invite.";
			}
		}else{
			$clear = false;
			$actions[] = 'Email is Invalid';
		}
		
		return array('success'=>empty($errors), 'errors'=>$errors, 'actions'=>$actions, 'clear'=>$clear);
	}
	
	function validateEmail($email){
		$email_pattern = '/^(?:(?:(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff]))(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff]|\x5c(?=[@,"\[\]\x5c\x00-\x20\x7f-\xff])|\.(?=[^\.])){1,62}(?:[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]|(?<=\x5c)[@,"\[\]\x5c\x00-\x20\x7f-\xff])|[^@,"\[\]\x5c\x00-\x20\x7f-\xff\.]{1,2})|"(?:[^"]|(?<=\x5c)"){1,62}")@(?:(?!.{64})(?:[a-zA-Z0-9][a-zA-Z0-9-]{1,61}[a-zA-Z0-9]\.?|[a-zA-Z0-9]\.?)+\.(?:xn--[a-zA-Z0-9]+|[a-zA-Z]{2,6})|\[(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])(?:\.(?:[0-1]?\d?\d|2[0-4]\d|25[0-5])){3}\])$/';
		return preg_match($email_pattern, $email);
	}
}
