<div style="float:left;">
	<button id="button-explore" onclick="setPlayMode('explore');">Explore</button>
	<button id="button-deploy" onclick="setPlayMode('deploy');">Fortify</button>
	<button id="button-build" onclick="setPlayMode('build');">Build</button>
	<button id="button-attack" onclick="setPlayMode('attack');">Attack</button>
</div>
<div style="float:right">
	
	<img id="loader" src="http://www.devwarrior.com/tilegame/img/loader.gif" style="display:none" />

	<?php if(!empty($contextual_buttons)):?>
		<?php foreach($contextual_buttons as $mode=>$buttons):?>
			<span id="buttons-<?=$mode?>" class="contextual-buttons">
				<?php foreach($buttons as $label=>$onclick):?>
					<button onclick="<?=$onclick?>"><?=$label?></button>
				<?php endforeach;?>
			</span>
		<?php endforeach;?>
	<?php endif;?>
	
	<button id="button-help" onclick="jQuery.lightbox('/tilegame/pages/help', {width:700, height:500});">Help</button>
</div>
