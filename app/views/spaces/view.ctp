<h2>Space Profile</h2>
<?php if(!empty($space)):?>
	<div id="space-detail">
		<?=$this->element('spaces/details', array('space'=>$space))?>
	</div>
	
	<?php if($space['Space']['user_id'] == $me['User']['id']):?>
		<h2>Build Something Here</h2>
		<div id="build-box">
			<script>$('#build-box').load('/tilegame/tiles/select/'+<?=$space['Space']['id']?>);</script>
		</div>
	<?php endif;?>
<?php else:?>
	<div>There is nothing here.</div>
<?php endif;?>