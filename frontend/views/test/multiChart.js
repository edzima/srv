var acc = $('#acc').highcharts();
			
		var gyro = $('#gyro').highcharts();
		
		var request = $request;
		
		if(!request) setInterval(requestData, 5000);
		
		var x =[], y=[], z=[], Gx=[], Gy=[], Gz=[];
		JSONlen = 0;
		
		function requestData(){
			$.getJSON('/storage/json/acc.json', function (data) {
				var start = new Date();
				
				var dataLen = data.length;
				var xLen = x.length;
						
				if (x.length<2000 && dataLen<2000){
					for (k = x.length; k < dataLen; k++){
					
						x.push([data[k][0],data[k][1]]);
						y.push([data[k][0],data[k][2]]);
						z.push([data[k][0],data[k][3]]);
						Gx.push([data[k][0],data[k][4]]);
						Gy.push([data[k][0],data[k][5]]);
						Gz.push([data[k][0],data[k][6]]);
					}
				}
				else
				{
					x=[],y=[],z=[],Gx=[],Gy=[],Gz=[];
					for(k=dataLen-2000;	k<dataLen;	k++) {
						x.push([data[k][0],data[k][1]]);
						y.push([data[k][0],data[k][2]]);
						z.push([data[k][0],data[k][3]]);
						Gx.push([data[k][0],data[k][4]]);
						Gy.push([data[k][0],data[k][5]]);
						Gz.push([data[k][0],data[k][6]]);
					}
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
					JSONlen = data.length;
					console.log('x len '+x.length);
				}
			
			});
		}
		
		requestData();