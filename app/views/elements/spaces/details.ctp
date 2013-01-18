<img style="float:left;" src="/tilegame/img/tiles/<?=$space['Tile']['image_url']?>" />
<table>
	<tr><th>Sector</th><td><?=$space['Space']['sector_id']?></td></tr>
	<tr><th>Coordinates</th><td>(<?=$space['Space']['x']?>,<?=$space['Space']['y']?>)</td></tr>
	<tr><th>Defending Units</th><td><?=$space['Space']['units']?></td></tr>
	<tr><th>Type</th><td><?=$space['Tile']['name']?></td></tr>
	<?php if(empty($space['Space']['user_id'])):?>
		<tr><th>Owner</th><td>Nobody</td></tr>
	<?php elseif($space['Space']['user_id'] == $me['User']['id']):?>
		<tr><th>Owner</th><td>You</td></tr>
	<?php else:?>
		<tr><th>Owner</th><td><?=$space['User']['name']?></td></tr>
	<?php endif;?>
	
	<?php if(!empty($space['Tile']['Resource']['name'])):?>
		<tr><th>Produces</th><td><?=$space['Tile']['Resource']['name']?></td></tr>
	<?php endif;?>
</table>
