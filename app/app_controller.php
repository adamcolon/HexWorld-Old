<?php
App::import('Vendor', 'facebook/facebook');

class AppController extends Controller {
	var $components = array('Session','RequestHandler');
	var $helpers = array('Session', 'Html', 'Form', 'Ajax', 'Time', 'Number');
	var $uses = array('User');
	
	// Global Variables
	var $facebook;
	var $game_settings;
	var $me;
	var $tileSpec = array('width'=>65, 'height'=>75);

	function beforeFilter(){
		$this->_loadSettings();
		$this->_loadUser();
		
		// Set User
		$user = $this->Session->read('me');	// Set "me" var from Session
		$this->me = $this->User->find('first', array('conditions' => array('User.id'=>$user['User']['id'])));		// Refresh User Object
		$this->set('me',$this->me); // Pass "me" to all views by default

		if (empty($this->me)){
			$this->Session->destroy();

			$message = "User Not Found";
			$this->set('messages', $message);
			$this->log("[".$this->RequestHandler->getClientIP()."][appController::beforeFilter] User Not Found", 'debug');
			
//			$this->render('/elements/alert/', 'default_static');
			$this->redirect('/pages/newUser');
//			echo $message;
			exit;
		}

		// Update Stats
		$this->User->UserStat->autoIncreaseStats($this->me);
	}

	function _reloadUser(){
		if(!empty($this->me)){
			if($user = $this->User->find('first', array('conditions'=>array('User.id'=>$this->me['User']['id'])))){
				$this->me = $user;
				$this->Session->write('me', $user);
				$this->set('me', $user);
				return $user;
			}
		}
	}
	
	function _loadSettings(){
		$this->game_settings = array(
			'fb_app_id'=>'100654090023609'
			,'fb_secret'=>'5600c6137183a424d8b432f0e5f8cf93'
			,'site_url'=>'http://www.devwarrior.com/tilegame/'
		);
		
		$this->set('game_settings',$this->game_settings); // Pass "game_settings" to all views by default
		Configure::write('game_settings', $this->game_settings);
		
		$this->set('controller', $this->name);
	}
	
	function _loadUser(){
		if (!($this->Session->check('me') && $this->Session->read('me'))){
			$this->Session->destroy();	// Destroy the Session

			if (!empty($this->params['isAjax'])){
				exit;
			}
			
			$fb_user = $this->_getFbUser();
#pr($fb_user);
			$this->log("[AppController:beforeFilter] session not found, user=[".$fb_user['id']."].", 'debug');
			$user_fbId = $fb_user['id'];
	
			if (!$user=$this->User->UserFacebook->find('first', array('conditions' => array('UserFacebook.id'=>$user_fbId), 'contain'=>array('UserFacebook')))){
				$this->log("[AppController:beforeFilter] User not found, newuser=[".$user_fbId."].", 'debug');

				$this->User->addNewUser($fb_user);
				$user=$this->User->UserFacebook->find('first', array('conditions' => array('UserFacebook.id'=>$user_fbId)));

				if(!empty($user)) $this->alertAdmin('New Player Joined', $user['User']['name']." [".$user['User']['id']."] has just joined as a new player.");
			}
			$this->Session->write('me', $user);
		}
	}
	
	function alertAdmin($headline, $message){
		$game_name = 'Tile Game Prototype';

		$subject = "[ $game_name Alert] $headline";
		$to = 'adamcolon@gmail.com';
		
		$this->log("* [alertAdmin] Sending Alert [$subject]", 'debug');
		mail($to, $subject, $message);
	}
	
	function _getFbUser(){
		$this->facebook = new Facebook(array(
				'appId' => $this->game_settings['fb_app_id'],
				'secret' => $this->game_settings['fb_secret'],
				'cookie' => true
		));
		
		if(is_null($this->facebook->getUser()))
		{
				header("Location:{$this->facebook->getLoginUrl(array('req_perms' => 'publish_stream'))}");
				exit;
		}
		
		return $this->facebook->api('/me');
	}

}
?>
