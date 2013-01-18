var clicking = false;
var mousetracking = {'x':0,'y':0};

var me = {};
var singleSpace = false;

var playMode = null;
var tileSpec = {};
var myCanvas = null;
var myContext = null;
var bufferCanvas = null;
var buffer = null;
var dataMap = [];
var selected = null;
var selected_highlight = [];
var mouseDrag = false;
var images = {};
var selected_tile = {};

var Spaces = {};

function BufferEffects(){
	this.blur = function(level, img) {
		var i, x, y;
		var passes = 1;
		
		if(!level) level=1;
		passes *= level;


		buffer.save();
		buffer.globalAlpha = 0.01;

		if(!img){
			img = bufferCanvas;
		}
		
		// Loop for each blur pass.
		for (i = 1; i <= passes; i++) {
			for (y = -1; y <= 1; y++) {
				for (x = -1; x <= 1; x++) {
					buffer.drawImage(img, x, y, bufferCanvas.width, bufferCanvas.height);
				}
			}
		}
		buffer.restore();

	}

	this.lighten = function(level){
		var opacity = 0.1;
		if(!level) level=1;
		opacity *= level;

		buffer.save();
		buffer.globalCompositeOperation = "lighter";
		buffer.fillStyle = "rgba(155, 155, 155, "+opacity+")";
		buffer.fillRect(0, 0, bufferCanvas.width, bufferCanvas.height);
		buffer.restore();
	}

	this.resize = function(width, height){
		bufferCanvas.width = width;
		bufferCanvas.height = height;
	}
	
	this.clear = function(){
		buffer.clearRect(0,0,bufferCanvas.width,bufferCanvas.height);
	}
	
	this.flip = function(direction){
		if(direction == 'left'){
			buffer.translate(bufferCanvas.width, 0);
			buffer.scale(-1,1);
		}else if(direction == 'right'){
			buffer.translate(0, bufferCanvas.height);
			buffer.scale(1,-1);
		}else if(direction == 'down'){
			buffer.translate(bufferCanvas.width, 0);
			buffer.scale(-1,1);
			buffer.translate(0, bufferCanvas.height);
			buffer.scale(1,-1);
		}
	}
	
	this.mask = function(){
		buffer.save();
		buffer.globalCompositeOperation = 'destination-out';
		buffer.drawImage(images['mask'], 0, 0, bufferCanvas.width, bufferCanvas.height);
		buffer.restore();
	}
	
	this.border = function(){
		buffer.save();
		buffer.drawImage(images['border'], 0, 0, bufferCanvas.width, bufferCanvas.height);
		this.blur(2, images['border']);
		buffer.restore();
	}
}

function HexSpace(space){
	this.space = space;
	this.neighbors = {};
	
	this.render = function(){
//		console.log('HexRender v1.2');

		myBuffer = new BufferEffects();
		
		myBuffer.resize(space.Position.width, space.Position.height);
		myBuffer.clear();
		buffer.save();
		
		checkCode = Math.abs(space.Space.x)+Math.abs(space.Space.y);	// To semi-randomly change tile
		if(checkCode % 4 == 0){
			myBuffer.flip('left');
		}else if(checkCode % 3 == 0){
			myBuffer.flip('right');
		}else if(checkCode % 2 == 0){
			myBuffer.flip('down');
		}
		buffer.drawImage(images[space.Tile.name], 0, 0, bufferCanvas.width, bufferCanvas.height);
		
		// Process Buffer
		myBuffer.blur(1);
		myBuffer.lighten(3);
		myBuffer.mask();
		myBuffer.border();

		// Copy the buffer onto the visible canvas
		myContext.drawImage(bufferCanvas, space.Position.left, space.Position.top, space.Position.width, space.Position.height);
	}

	this.updateNeighbors = function(){
		// Upper Left
		neighbor_idx = space.Space.x + ',' + (space.Space.y+1);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.lr = this;
			this.neighbors.ul = Spaces[neighbor_idx];
		}
			
		// Upper Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y+1);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ll = this;
			this.neighbors.ur = Spaces[neighbor_idx];
		}

		// Left
		neighbor_idx = (space.Space.x-1) + ',' + (space.Space.y);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.r = this;
			this.neighbors.l = Spaces[neighbor_idx];
		}

		// Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.l = this;
			this.neighbors.r = Spaces[neighbor_idx];
		}

		// Lower Left
		neighbor_idx = space.Space.x + ',' + (space.Space.y-1);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ur = this;
			this.neighbors.ll = Spaces[neighbor_idx];
		}

		// Lower Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y-1);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ul = this;
			this.neighbors.lr = Spaces[neighbor_idx];
		}
	}
}

function SquareSpace(space){
	this.space = space;
	this.neighbors = {};
	
	this.render = function(){
		console.log('SquareRender v1.2');
console.log(myCanvas);
		myCanvas.style.backgroundImage = "url(/tilegame/img/tiles/"+space.Tile.image_url+")";
console.log('space:');
console.log(space);
		myContext.drawImage(images['user'], 500, 500);
	}

	this.updateNeighbors = function(){
console.log('updateNeigbors v1.2');
console.log(space);
		// Upper Left
		neighbor_idx = (space.Space.x-1) + ',' + (space.Space.y+1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.lr = this;
			this.neighbors.ul = Spaces[neighbor_idx];
		}
			
		// Upper
		neighbor_idx = space.Space.x + ',' + (space.Space.y+1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.d = this;
			this.neighbors.u = Spaces[neighbor_idx];
		}

		// Upper Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y+1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ll = this;
			this.neighbors.ur = Spaces[neighbor_idx];
		}

		// Left
		neighbor_idx = (space.Space.x-1) + ',' + (space.Space.y);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.r = this;
			this.neighbors.l = Spaces[neighbor_idx];
		}

		// Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.l = this;
			this.neighbors.r = Spaces[neighbor_idx];
		}

		// Lower Left
		neighbor_idx = (space.Space.x-1) + ',' + (space.Space.y-1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ur = this;
			this.neighbors.ll = Spaces[neighbor_idx];
		}

		// Lower
		neighbor_idx = space.Space.x + ',' + (space.Space.y-1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.r = this;
			this.neighbors.d = Spaces[neighbor_idx];
		}

		// Lower Right
		neighbor_idx = (space.Space.x+1) + ',' + (space.Space.y-1);
console.log(neighbor_idx);
console.log(Spaces[neighbor_idx]);
		if(Spaces[neighbor_idx]){
			Spaces[neighbor_idx].neighbors.ul = this;
			this.neighbors.lr = Spaces[neighbor_idx];
		}
	}
}

window.onresize = function(){
console.log("onresize");
	resizeCanvas();
	refreshDataMap();
}

window.onload = function(){
console.log("onload");
	myCanvas = document.getElementById('myCanvas');
//	myCanvas.addEventListener("click", click, false);
//	myCanvas.addEventListener("mousedown", mousedown, false);
//	myCanvas.addEventListener("mouseup", mouseup, false);

	myContext = myCanvas.getContext('2d');
	myContext.textAlign = 'center';

	bufferCanvas = document.getElementById("buffer");
	buffer = bufferCanvas.getContext("2d");

	$.getJSON('http://www.devwarrior.com/tilegame/tiles/getTiles', function(data){preloadImages(data);});
	$.getJSON('http://www.devwarrior.com/tilegame/users/getMe', function(data){
		me=data;
		console.log('me:');
		console.log(me);
		
		if(me.UserLocation.location_x && me.UserLocation.location_y){
			singleSpace = true;
		}
		
//		$.getJSON('http://www.devwarrior.com/tilegame/users/selectedTile/', function(data){selected_tile = data;});
		resizeCanvas();
		setTimeout(function(){init();}, 3000);
	});

	$('#myCanvas').mousedown(function(e){
		clicking = true;
		mousetracking = {'x':e.pageX, 'y':e.pageY};
	});

	$(document).mouseup(function(){
		clicking = false;
		mousetracking = {'x':0,'y':0};
	})

	$('#myCanvas').mousemove(function(e){
		if(clicking == false) return;
		
		mousetracking_difference = {'x':(mousetracking.x-e.pageX), 'y':(mousetracking.y-e.pageY)};
		canvas_offset = $('#myCanvas').offset();
		$('#myCanvas').offset({'left':(canvas_offset.left - mousetracking_difference.x), 'top':(canvas_offset.top - mousetracking_difference.y)});
		mousetracking = {'x':e.pageX, 'y':e.pageY};
	});
	
	$('#myCanvas').dblclick(function(e){
		console.log('dblclick fired');
		if(singleSpace){
			$.getJSON('http://www.devwarrior.com/tilegame/users/positionChange/'+me.UserLocation.sector_id+'/'+me.UserLocation.space_x+'/'+me.UserLocation.space_y+'/', function(data){
				refreshDataMap();
			});
		}else{
			$.getJSON('http://www.devwarrior.com/tilegame/users/positionChange/'+me.UserLocation.sector_id+'/'+me.UserLocation.space_x+'/'+me.UserLocation.space_y+'/0/0', function(data){
				refreshDataMap();
			});
		}
	});
}

function init(){
	setPlayMode('explore');
	refreshDataMap();
	setInterval(function(){refreshDataMap();}, 30000);
}

function getMe(){
	$.getJSON('http://www.devwarrior.com/tilegame/users/getMe', function(data){
		me=data;
		console.log('me:');
		console.log(me);
		
		if(me.UserLocation.location_x && me.UserLocation.location_y){
			singleSpace = true;
		}else{
			singleSpace = false;
		}
	});
}

function preloadImages(data){
	console.log('Preloading Tiles...');
 	for(var i in data){
		var name = data[i].Tile.name;

		if(!images.name){
			var image = new Image();
			image.src = 'http://www.devwarrior.com/tilegame/img/tiles/'+data[i].Tile.image_url;
			images[name] = image;
		}
	}

	// Preload Border
	var image = new Image();
	image.src = 'http://www.devwarrior.com/tilegame/img/tiles/tile_border.png';
	images['border'] = image;

	var image = new Image();
	image.src = 'http://www.devwarrior.com/tilegame/img/tiles/tile_mask.png';
	images['mask'] = image;

	var image = new Image();
	image.src = 'http://www.devwarrior.com/tilegame/img/user_icon.png';
	images['user'] = image;
		
	console.log(images);
}

function setPlayMode(mode){
	resetButtons();
	
	playMode = mode;
	$('#button-'+mode).css('background-color', '#88FF88');
	$('#button-'+mode).css('font-weight', 'bold');
	$('#buttons-'+mode).show();
}

function resetButtons(){
	var buttons = ['explore','deploy', 'attack', 'build'];
	
	for (var mode in buttons){
		button_mode = '#button-'+buttons[mode];
		button_group = '#buttons-'+buttons[mode];
		$(button_mode).css('background-color', 'grey');
		$(button_mode).css('font-weight', 'normal');
		$(button_group).hide();
	}
}

function resizeCanvas(){
	new_width = window.innerWidth-30;
	new_height = window.innerHeight-$('#myCanvasWindow').offsetTop;
	
	$('#myCanvasWindow').width = new_width;
	$('#myCanvasWindow').height = new_height;
//	$('#myCanvas').width = '120%';
//	$('#myCanvas').height = '120%';
	$('#footer').offset(0, myCanvas.height+myCanvas.top-30);
}

function refreshDataMap(){
	getMe();
	
console.log("refreshDataMap():"+singleSpace);
	$('#loader').show();
	
	if(singleSpace){
		url = 'http://www.devwarrior.com/tilegame/spaces/getCurrentLocation';
	}else{
		url = 'http://www.devwarrior.com/tilegame/spaces/getDataMap/'+myCanvas.width+'/'+myCanvas.height;
	}
	
	$.getJSON(url, function(data){
		$('#loader').hide();
		console.log(data);
		tileSpec = data.tileSpec;
		dataMap = data.dataMap;
		buildGameBoard();
		$('#header').load('/tilegame/users/getHeader');
	});
}

function buildGameBoard(){
	console.log('buildGameBoard v1.3');
	myContext.clearRect(0,0,myCanvas.width,myCanvas.height);
	Spaces = {};

	if(singleSpace){
		Space = new SquareSpace(dataMap);
		Space.render();
	}else{
		for(var i in dataMap){
			for(var j in dataMap[i]){
				idxSpace = i+','+j;
				Spaces[idxSpace] = new HexSpace(dataMap[i][j]);
				Spaces[idxSpace].render();
				Spaces[idxSpace].updateNeighbors();
			}
		}
		selected_highlight.forEach(highlightSpace);
		selected_highlight.forEach(arrowSpace);
		selected_highlight = [];
		
		console.log('Spaces');
		console.log(Spaces);
	}
}

function drawTile(space){
	if(images[space.Tile.name]){
		var image = images[space.Tile.name];
	}else{
		var image = new Image();
		image.src = '/tilegame/img/tiles/'+space.Tile.image_url;
	}

	if (!(space.Space.x == 0 && space.Space.y == 0)){
		writeText(space);
	}
	
	// Mark if Owner
	if(space.User.id){
		highlightTile(space, 0.25);
		if(space.User.id && space.User.id != me.User.id){
			borderTile(space, '#aa0000');
		}
	}

	if(selected && selected.Space.id == space.Space.id){
		space.target = 0;
		selected_highlight.push(space);
	}else if(selected && spaceIsTargetable(selected, space)){
		space.target = 1;
		selected_highlight.push(space);
	}
}

function writeText(space){
	if(space.User.id && space.User.id == me.User.id){
		var color = '#00aa00';
	}else if(space.User.id && space.User.id != me.User.id){
		var color = '#ff0000';
	}else{
		var color = '#000000';
	}
	
	myContext.textAlign = 'center';
	myContext.font = 'bold 17px Arial Black';
	myContext.fillStyle = '#ffffff';
	myContext.fillText(space.Space.units, space.Position.center.x, space.Position.center.y);
	myContext.font = '14px Arial Black';
	myContext.fillStyle = color;
	myContext.fillText(space.Space.units, space.Position.center.x, space.Position.center.y-1);
}

function highlightSpace(space){
	highlightTile(space, 0.4);
}

function arrowSpace(space){
	if(space.target) drawLine(selected.Position.center.x, selected.Position.center.y, space.Position.center.x, space.Position.center.y, '#000000');
}

function tilePosition(tileSpec, x, y){
	var position = {};
	
	position.x = x;
	position.y = y;
	position.width = tileSpec.width;
	position.height = tileSpec.height;
	position.left = position.width * x;
	if(y%2 == 1) position.left += (position.width/2);
	
	position.top = (((position.height*3)/4) * y);
	position.center = {'x': (position.left + (position.width/2)),'y': (position.top + (position.height/2))};
	
	return position;
}

function inHex(x,y, position){
	if((x-position.center.x)^2 + (y-position.center.y)^2 < (position.width/2)^2){
		return true;
	}
}

function tilePositionByCoordinates(tileSpec, coord){
	y = Math.floor(coord.y/((tileSpec.height*3)/4));
	if(y%2 == 1) coord.x -= (tileSpec.width/2);
	x = Math.floor(coord.x / tileSpec.width);
	
	position = tilePosition(tileSpec, x,y);
	return position;
}

function getCoordDiff(start, end){
	var diff = {};
	
	diff.x = end.x - start.x;
	diff.y = end.y - start.y;
	
	return diff;
}

function getMouseCoordinates(e){
	var x;
	var y;
	if (e.pageX || e.pageY) { 
	  x = e.pageX;
	  y = e.pageY;
	}else { 
	  x = e.clientX + document.body.scrollLeft + document.documentElement.scrollLeft; 
	  y = e.clientY + document.body.scrollTop + document.documentElement.scrollTop; 
	}
	
	x -= myCanvas.offsetLeft;
	y -= myCanvas.offsetTop;
	
	return {'x':x, 'y':y};
}
 
function mousedown(e){
	if(!mouseDrag){
		mouseDrag = true;
	}
}

function mouseup(e){
	mouseDrag = false;
}

function mousemove(e){
	if (mouseDrag){
		myCanvas.style.cursor='hand';
		
		var mouseCoord = getMouseCoordinates(e);
console.log(mouseCoord);
		refreshDataMap();
	}
}

function getTileValue(space){
	return (space.Space.units && space.Space.units>0)?space.Space.units:'';
}

function highlightTile(space, opacity){
	bufferCanvas.width = space.Position.width;
	bufferCanvas.height = space.Position.height;
	buffer.clearRect(0,0,bufferCanvas.width,bufferCanvas.height);
	
	// Draw your image on the buffer
	buffer.drawImage(images[space.Tile.name], 0, 0, tileSpec.width, tileSpec.height);

	// Draw a rectangle over the image using a nice translucent overlay
	if(space.Highlight.color){
		color = hexToColor(space.Highlight.color);
	}else{
		color = hexToColor('#ffeedd');
	}
	
	buffer.save();
	buffer.globalCompositeOperation = "source-in";
	buffer.fillStyle = "rgba("+color.red+", "+color.green+", "+color.blue+", "+opacity+")";
	buffer.fillRect(0	, 0, bufferCanvas.width, bufferCanvas.height);
	buffer.restore();

	buffer.save();
	buffer.globalCompositeOperation = 'destination-out';
	buffer.drawImage(images['mask'], 0, 0, bufferCanvas.width, bufferCanvas.height);
	buffer.restore();

	// Copy the buffer onto the visible canvas
	myContext.drawImage(bufferCanvas, space.Position.left, space.Position.top, space.Position.width, space.Position.height);
}

function borderTile(space, color){
	bufferCanvas.width = space.Position.width;
	bufferCanvas.height = space.Position.height;
	buffer.clearRect(0,0,bufferCanvas.width,bufferCanvas.height);
	
	// Draw your image on the buffer
	buffer.drawImage(images['border'], 0, 0, tileSpec.width, tileSpec.height);

	// Draw a rectangle over the image using a nice translucent overlay
	if(color){
		color = hexToColor(color);
	}else if(space.Highlight.color){
		color = hexToColor(space.Highlight.color);
	}else{
		color = hexToColor('#555555');
	}

	buffer.save();
	buffer.globalCompositeOperation = "source-in";
	buffer.fillStyle = "rgba("+color.red+", "+color.green+", "+color.blue+", 0.4)";
	buffer.fillRect(0, 0, tileSpec.width, tileSpec.height);
	buffer.restore();

	// Copy the buffer onto the visible canvas
	myContext.drawImage(bufferCanvas, space.Position.left, space.Position.top, tileSpec.width, tileSpec.height);
}

function drawLine(start_x, start_y, end_x, end_y, color){
	if (!color) color="#880000";
	
	myContext.beginPath();
	myContext.moveTo(start_x, start_y);
	myContext.lineTo(end_x, end_y);
	myContext.strokeStyle=color; 
	myContext.lineWidth=2;
	myContext.lineCap="round";
	myContext.stroke();

	myContext.beginPath();
	myContext.arc(start_x,start_y, 4, 0, 2*Math.PI,false);
	myContext.fillStyle=color;
	myContext.fill();
	myContext.strokeStyle=color; 
	myContext.stroke();
}

function hexToColor(hex){
	hex = (hex.charAt(0)=="#") ? hex.substring(1,7):hex;
	
	var color = {
		'red': parseInt((hex).substring(0,2),16)
		,'green': parseInt((hex).substring(2,4),16)
		,'blue': parseInt((hex).substring(4,5),16)
	}
	
	return color;
}

function positionNavButtons(){
	$('#navigation').offset($('#myCanvas').offset());
	$('#navigation').show();
}
