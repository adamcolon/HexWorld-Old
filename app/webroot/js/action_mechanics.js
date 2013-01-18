
function targetCanAttack(space){
	if (space && space.Space.units>1){
		console.log('targetCanAttack');
		return true;
	}
	console.log('targetCanAttack:NO');
}

function meIsaVirgin(){
	if(me.UserStat.spaces_owned.value == 0){
		console.log('meIsaVirgin');
		return true;
	}
	console.log('meIsaVirgin:NO');
}

function targetIsMine(space){
console.log(me);
console.log(space);
	if (space && space.User && space.User.id == me.User.id){
		console.log('targetIsMine');
		return true;
	}
	console.log('targetIsMine:NO');
}

function targetIsOwned(space){
	if (space && space.User.id){
		console.log('targetIsOwned');
		return true;
	}
	console.log('targetIsOwned:NO');
}

function targetHasDifferentOwner(source, target){
	if (!spaceIsSame(source, target) && (!target.User || source.User.id != target.User.id)){
		console.log('targetHasDifferentOwner');
		return true;
	}
	console.log('targetHasDifferentOwner:NO');
}

function targetIsNeighbor(source, target){
	if (
		(source.Space.y == target.Space.y && Math.abs(source.Space.x-target.Space.x) == 1)
		|| (source.Space.x == target.Space.x && Math.abs(source.Space.y-target.Space.y) == 1)
		|| ((source.Space.x-((source.Space.y+1)%2) == target.Space.x) && Math.abs(source.Space.y-target.Space.y) == 1)
		|| ((source.Space.x-(source.Space.y%2) == target.Space.x) && Math.abs(source.Space.y-target.Space.y) == 1)
	){
		console.log('targetIsNeighbor');
		return true;
	}

	console.log('targetIsNeighbor:NO');
}

function targetIsNeighbor_old(source, target){
	if ((source.Space.y == target.Space.y && Math.abs(source.Space.x-target.Space.x) == 1)
			|| (Math.abs(source.Space.y-target.Space.y) == 1 && source.Space.x == target.Space.x-(source.Space.y%2))
			|| (Math.abs(source.Space.y-target.Space.y) == 1 && source.Space.x-(target.Space.y%2) == target.Space.x)
	){
		console.log('targetIsNeighbor');
		return true;
	}
	console.log('targetIsNeighbor:NO');
}

function spaceIsSame(source, target){
	if(source.Space.id == target.Space.id){
		console.log('spaceIsSame');
		return true;
	}
	console.log('spaceIsSame:NO');
}

function actionRequiresSelection(){
	if(playMode == 'explore'){
		console.log('actionRequiresSelection:NO');
		return false;
	}else if(playMode == 'attack'){
		console.log('actionRequiresSelection');
		return true;
	}else if(playMode == 'deploy'){
		console.log('actionRequiresSelection:NO');
		return false;
	}else if(playMode == 'build'){
		console.log('actionRequiresSelection:NO');
		return false;
	}
	
	console.log('actionRequiresSelection:NO');
}

function spaceIsSelectable(space){
	if(playMode == 'explore'){
		console.log('spaceIsSelectable:NO');
		return false;
	}else if(playMode == 'attack'){
		if (targetIsMine(space) && targetCanAttack(space)){
			console.log('spaceIsSelectable');
			return true;
		}
	}else if(playMode == 'deploy'){
		console.log('spaceIsSelectable:NO');
		return false;
	}else if(playMode == 'build'){
		console.log('spaceIsSelectable:NO');
		return false;
	}
	
	console.log('spaceIsSelectable:NO');
}

function spaceIsTargetable(selected, target){
	if(playMode == 'explore'){
		if (targetIsMine(target) || meIsaVirgin()){
			console.log('spaceIsTargetable');
			return true;
		}
	}else if(playMode == 'attack'){
		if (targetHasDifferentOwner(selected, target) && targetIsNeighbor(selected, target)){
			console.log('spaceIsTargetable');
			return true;
		}
	}else if(playMode == 'deploy'){
		if (targetIsMine(target) || (!targetIsOwned(target) && (meIsaVirgin()))){
			console.log('spaceIsTargetable');
			return true;
		}
	}else if(playMode == 'build'){
		if (targetIsMine(target)){
			console.log('spaceIsTargetable');
			return true;
		}
	}
	
	console.log('spaceIsTargetable:NO');
}

function spaceDoAction(selected, target){
	var url = null;
	console.log('doAction:'+playMode);
	
	if(playMode == 'explore'){
		url = 'http://www.devwarrior.com/tilegame/spaces/explore/'+target.Space.id;
	}else if(playMode == 'attack'){
		url = 'http://www.devwarrior.com/tilegame/spaces/attack/'+selected.Space.id+'/'+target.Space.id;
	}else if(playMode == 'deploy'){
		url = 'http://www.devwarrior.com/tilegame/spaces/deploy/'+target.Space.id;
	}else if(playMode == 'build'){
		showSpaceDetails(target);
		//url = 'http://www.devwarrior.com/tilegame/spaces/build/'+target.Space.id;
	}
	
	if(url){
		$.getJSON(url, function(data){
			console.log(playMode);
			console.log(data);
		});
		setTimeout('refreshDataMap()', 1000);
	}
}

function click(e){
	console.log(playMode);
	mouseDrag = false;
	myCanvas.style.cursor='hand';

	var mouseCoord = getMouseCoordinates(e);
	position = tilePositionByCoordinates(tileSpec, mouseCoord);

	if (dataMap[position.x] && dataMap[position.x][position.y]){
		space = dataMap[position.x][position.y];
		
// Travel
console.log('Traveling:'+space.Space.x+','+space.Space.y);
$.get('/tilegame/users/positionChange/set/'+space.Space.x+'/'+space.Space.y, function(data){refreshDataMap();});
return;

		if (actionRequiresSelection()){
			if(selected){
				console.log('selected');
				if(spaceIsSame(selected, space)){
					console.log('unselecting');
					selected = null;	// Unselect
					buildGameBoard();
				}else{
					console.log('trying to target');
					if(spaceIsTargetable(selected, space)){
						spaceDoAction(selected, space);
						console.log('unselecting');
						selected = null;
					}
				}
			}else if(spaceIsSelectable(space)){
				selected = space;
				buildGameBoard();
			}
		}else{
			if(spaceIsTargetable(null, space)){
				spaceDoAction(null, space);
			}
		}
	}else{
		console.log(position);
		console.log(dataMap);
		console.log('no tile');
	}
}

function changeSpaceTile(space_id, tile_id){
	console.log('changeSpaceTile.'+space_id+','+tile_id);
	$.get('http://www.devwarrior.com/tilegame/spaces/build/'+space_id+'/'+tile_id, function(data){
		console.log('changeSpaceTile:Complete');
		$('#space-detail').load('http://www.devwarrior.com/tilegame/spaces/view/'+space_id+'/1');
		refreshDataMap();
	});
}

function showSpaceDetails(space){
	jQuery.lightbox('/tilegame/spaces/view/'+space.Space.id, {width:700, height:400});
}