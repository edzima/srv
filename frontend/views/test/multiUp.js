var acc = $('#acc').highcharts();
	
var gyro = $('#gyro').highcharts();

var request = $request;

if(!request) setInterval(requestData, 5000);

var x =[], y=[], z=[], Gx=[], Gy=[], Gz=[];

dataJSON = {
	length : 0,
	delta : 0,
};

function requestData(){
	$.getJSON('/storage/json/acc.json', function (data) {
		var start = new Date();
		
		var dataLen = data.length;
		var xLen = x.length;
		console.log('data len: '+dataLen);
		if (dataJSON.length < dataLen){
			
			if(dataJSON.length>500){
				dataJSON.delta = dataLen - dataJSON.length;
				console.log('x > data');
				//delete element
				for(i=0; i<dataJSON.delta; i++){
					x.shift();
					y.shift();
					z.shift();
					Gx.shift();
					Gy.shift();
					Gz.shift();	
					
				}
			}
			
			for (k = dataJSON.delta; k < dataLen; k++){
			
				x.push([data[k][0],data[k][1]]);
				y.push([data[k][0],data[k][2]]);
				z.push([data[k][0],data[k][3]]);
				Gx.push([data[k][0],data[k][4]]);
				Gy.push([data[k][0],data[k][5]]);
				Gz.push([data[k][0],data[k][6]]);
			}
			
			acc.series[0].update({
			//pointStart: newSeries[0].pointStart,
				data: x
			}, true);
			acc.series[1].update({
			//pointStart: newSeries[1].pointStart,
				data: y
			}, true);
			acc.series[2].update({
			//pointStart: newSeries[2].pointStart,
				data: z
			}, true);
			
			gyro.series[0].update({
			//pointStart: newSeries[0].pointStart,
				data: Gx
			}, true);
			gyro.series[1].update({
			//pointStart: newSeries[1].pointStart,
				data: Gy
			}, true);
			gyro.series[2].update({
			//pointStart: newSeries[2].pointStart,
				data: Gz
			}, true);
			
			var time = new Date() - start;
			console.log('Script time: '+time);
	
		}
		dataJSON.length = x.length;
		console.log('x len '+x.length);
	
	});
}


requestData();


function requestData(){
			$.getJSON('/storage/json/acc.json', function (data) {
				var start = new Date();
				console.log('jestem');
					var ser = new Date();
				var dataLen = data.length;
				var xLen = x.length;
				console.log('data Len: '+dataLen);
				var xCount = 500;
				if (xLen<xCount){
					for (k = xLen; k < dataLen; k++){
					if (k%3==0){

						//acc.series[0].addPoint([data[k][0],data[k][1]], true, true);
				
						//x.push([data[k][0],data[k][1]]);
					/*	y.push([data[k][0],data[k][2]]);
						z.push([data[k][0],data[k][3]]);
						Gx.push([data[k][0],data[k][4]]);
						Gy.push([data[k][0],data[k][5]]);
						Gz.push([data[k][0],data[k][6]]);*/
						}
					}
				}
				else
				{
					delta = x.length-xCount;
					//x= x.slice(delta,xLen);
					
					x=[],y=[],z=[],Gx=[],Gy=[],Gz=[];
					for(k=dataLen-xCount;	k<dataLen;	k++) {
					if (k%3==0){
						x.push([data[k][0],data[k][1]]);
						/*y.push([data[k][0],data[k][2]]);
						z.push([data[k][0],data[k][3]]);
						Gx.push([data[k][0],data[k][4]]);
						Gy.push([data[k][0],data[k][5]]);
						Gz.push([data[k][0],data[k][6]]);*/
					}
					}
				}
				var timeSer = new Date() - ser;
					console.log('Add ser time: '+timeSer);
			/*
					acc.series[0].update({
					//pointStart: newSeries[0].pointStart,
						data: x
					}, true);
					
					acc.series[1].update({
					//pointStart: newSeries[1].pointStart,
						data: y
					}, true);
					acc.series[2].update({
					//pointStart: newSeries[2].pointStart,
						data: z
					}, true);
					
					gyro.series[0].update({
					//pointStart: newSeries[0].pointStart,
						data: Gx
					}, true);
					gyro.series[1].update({
					//pointStart: newSeries[1].pointStart,
						data: Gy
					}, true);
					gyro.series[2].update({
					//pointStart: newSeries[2].pointStart,
						data: Gz
					}, true);
					*/
					var time = new Date() - start;
					
					
					console.log('Script time: '+time);
				
					JSONlen = data.length;
					console.log('x len '+x.length);
				
			
			});
		}
		