<?php
class EmailInvitesController extends InvitesAppController {
	var $uses = array('User');
	var $components = array('Email');
	var $autoRender = false;
	
	public function index(){
		echo __METHOD__;
	}
	 
	public function sendInvites(){
		echo __METHOD__."<br>";
		$errors = $actions = array();
		
		$this->autoRender = true;
		
		if(!empty($this->params['form']['emails'])){
			$emails_raw = $this->params['form']['emails'];
			$emails_raw = str_replace(',', "\n", $emails_raw);
			$emails = explode("\n", $emails_raw);
			
			if(!empty($emails)){
				foreach($emails as $email){
					$email = trim($email);
					$result = $this->_sendInvite($email);
					if($result['success']){
						$actions = array_merge($actions, $result['actions']);
					}else{
						$actions = array_merge($actions, $result['actions']);
						$errors = array_merge($errors, $result['errors']);
					}
				}
			}
		}
		
		echo "Actions:";pr($actions);
		echo "Errors:";pr($errors);
	}
	
	function _sendInvite($email){
echo __METHOD__.", email:{$email}<br>";
		$errors = $actions = array();
		
		if(!$this->User->findByEmail($email)){
			$result = $this->EmailInvite->clearToSend($this->me['User']['id'], $email);
			if($result['success']){
				if($result['clear']){
					echo "Sending Email: {$email}<br>";
					$invite['EmailInvite'] = array(
						'user_id'=>$this->me['User']['id']
						,'email'=>$email
					);
					$this->EmailInvite->create();
					$this->EmailInvite->save($invite);
				}else{
					$actions[] = "Email Not Sent";
					$actions = array_merge($actions, $result['actions']);
				}
			}else{
				$actions = array_merge($actions, $result['actions']);
				$errors = array_merge($errors, $result['errors']);
			}
		}else{
			$actions[] = "Email not Sent, Already a User";
		}
		
		return array('success'=>empty($errors), 'errors'=>$errors, 'actions'=>$actions);
	}
	
}