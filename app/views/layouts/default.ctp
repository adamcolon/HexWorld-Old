<!DOCTYPE html>
<html>
	<head>
		<title>HexWorld::<?=$title_for_layout?></title>
		<script src="http://ajax.googleapis.com/ajax/libs/jquery/1.5/jquery.min.js"></script>

		<script type="text/javascript" src="/tilegame/js/lightbox/jquery.lightbox.js"></script>
		<script type="text/javascript" src="/tilegame/js/jquery.dataTables.js"></script>
		<link rel="stylesheet" type="text/css" href="/tilegame/js/lightbox/themes/evolution-dark/jquery.lightbox.css">

		<?=$html->css('styles')?>
		<?=$html->script('action_mechanics')?>

		<script type="text/javascript">jQuery(document).ready(function(){ jQuery('.lightbox').lightbox(); });</script>
		<?=$scripts_for_layout?>
	</head>
	<body>
		<script>if (window.top!=window.self) window.top.location = window.self.location;</script>
		
		<div id="header">
			<?=$this->element('header', array('me', $me));?>
		</div>
		
		<div id="action-bar">
			<?=$this->element('action_bar');?>
		</div>
		<div id="body" style="clear:both">
			<?=$content_for_layout?>
		</div>
		<div id="footer" style="text-align:center;font-size:small;">Copyright &copy; 2011 <a href="mailto:adam@blackdagger.com?subject=HexWorld%20Feedback">Adam Colon</a>, All Rights Reserved</div>
		
		<script>
			$(document).ready(function() {
				setInterval(function(){ $('#header').load('/tilegame/users/getHeader'); }, 6000);
				if(<?=$me['UserStat']['spaces_owned']['value']?> == 0){
					console.log(<?=$me['UserStat']['spaces_owned']?>);
					jQuery.lightbox('/tilegame/pages/help', {width:700, height:500});
				}
			});
		</script>
		
		<?=$this->element('analytics');?>
	</body>
</html>