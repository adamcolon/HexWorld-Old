<img src="/tilegame/img/navigation.png" alt="Navigation" usemap="#navigation" id="navigation" />
<map name="navigation">
	<area shape="rect" coords="0,0,30,30" onclick="$.get('/tilegame/users/positionChange/add/-2/-2', function(data){refreshDataMap();});" href="#" />
	<area shape="rect" coords="30,0,60,30" onclick="$.get('/tilegame/users/positionChange/add/0/-2', function(data){refreshDataMap();});" href="#" />
	<area shape="rect" coords="60,0,90,30" onclick="$.get('/tilegame/users/positionChange/add/2/-2', function(data){refreshDataMap();});" href="#" />

	<area shape="rect" coords="0,30,30,60" onclick="$.get('/tilegame/users/positionChange/add/-2/0', function(data){refreshDataMap();});" href="#" />
	<area shape="rect" coords="60,30,90,60" onclick="$.get('/tilegame/users/positionChange/add/2/0', function(data){refreshDataMap();});" href="#" />

	<area shape="rect" coords="0,60,30,90" onclick="$.get('/tilegame/users/positionChange/add/-2/2', function(data){refreshDataMap();});" href="#" />
	<area shape="rect" coords="30,60,60,90" onclick="$.get('/tilegame/users/positionChange/add/0/2', function(data){refreshDataMap();});" href="#" />
	<area shape="rect" coords="60,60,90,90" onclick="$.get('/tilegame/users/positionChange/add/2/2', function(data){refreshDataMap();});" href="#" />

</map>