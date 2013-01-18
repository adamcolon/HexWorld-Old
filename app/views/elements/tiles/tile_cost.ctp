<?php if(!empty($tile['ResourceCost'])):?>
	<?php foreach($tile['ResourceCost'] as $cost):?>
		<?=pr($cost)?>
	<?php endforeach;?>
<?php endif;?>