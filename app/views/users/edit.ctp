<?=$html->script('http://meta100.github.com/mColorPicker/javascripts/mColorPicker_min.js', true);?>

<div style="background-color:#000000;color:#ffffff;">
	<h1>Update Your Profile</h1>
	<table>
		<tr><td>Change your <b>Name</b></td><td><input type="text" id="UserName" maxlength="20" value="<?=$me['User']['name']?>" /></td><td><small>(min 4 chars)</small></td></tr>
		<tr><td>Change your <b>Color</b></td><td><input type="color" data-hex="true" data-text="hidden" id="UserColor" value="<?=$me['User']['color']?>" /></td><td></td></tr>
		<tr><td>Tile Size</td><td><input type="text" id="UserTileSize" value="<?=$me['User']['tile_size']?>" /></td></tr>
	</table>
</div>
<div style="text-align:right"><button id="UserEditFormSave">Save</button><button onclick="$.lightbox().close();">Cancel</button></div>

<script>
	$('#UserEditFormSave').click(function() {
		var newData = {};
		if($("#UserName").val() != '<?=$me['User']['name']?>' && $("#UserName").val().length >= 4) newData['name'] = $("#UserName").val();
		if($("#UserColor").val() != '<?=$me['User']['color']?>') newData['color'] = $("#UserColor").val();
		if($("#UserTileSize").val() != '<?=$me['User']['color']?>') newData['tile_size'] = $("#UserTileSize").val();
		if(newData){
			newData['update'] = 1;
			jQuery.getJSON("/tilegame/users/edit", newData, function(data){
				$.lightbox().close();
			});
		}
	});
</script>