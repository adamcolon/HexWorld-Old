<?php
class UserFacebook extends AppModel{
	var $name = 'UserFacebook';
	var $belongsTo = array('User');
	var $useTable = 'user_facebook';

}
?>