<?php if(!empty($tiles)):?>
	<table id="tiles" class="display">
		<thead>
			<tr>
				<th>Select</th>
				<th>Name</th>
				<th>Cost</th>
				<th>Attack Bonus</th>
				<th>Defense Bonus</th>
				<th>Produces</th>
				<th>Actions</th>
			</tr>
		</thead>
		<tbody>
			<?php foreach($tiles as $tile):?>
				<tr>
					<td>
						<?php if($me['UserStat']['reserve_units'] >= $tile['Tile']['cost']):?>
							<a href="#" onclick="changeSpaceTile(<?=$space_id?>,<?=$tile['Tile']['id']?>);"><img src="/tilegame/img/tiles/<?=$tile['Tile']['image_url']?>" /></a>
						<?php else:?>
							<img src="/tilegame/img/tiles/<?=$tile['Tile']['image_url']?>" />
						<?php endif;?>
					</td>
					<td><?=$tile['Tile']['name']?></td>
					<td><?=number_format($tile['Tile']['cost'])?> units</td>
					<td><?=$tile['Tile']['attack_modifier']?></td>
					<td><?=$tile['Tile']['defense_modifier']?></td>
					<td><?=$tile['Resource']['name']?></td>
					<td><?=$this->element('tiles/tile_cost')?></td>
				</tr>
			<?php endforeach;?>
		</tbody>
	</table>

	<script>
		$(document).ready(function() {
			$('#tiles').dataTable();
		} );
	</script>
<?php else:?>
	I got nothing.
<?php endif;?>