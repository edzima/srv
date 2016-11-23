<?php
use yii\helpers\Html;

use miloschuman\highcharts\Highcharts;

?>
<div class="col-md-12">
	
	<div id="acc">
	<?= Highcharts::widget([
		   'options' => [
				'title' => ['text' => 'Accelerometer'],
				'chart' => [
				  'renderTo' => 'acc',
				  'zoomType'=> 'x',
				  'type'=> 'spline',
				  'animation' => 'Highcharts.svg',
				],   

				'xAxis' => [
					 'type' => 'datetime',
			
				],

				'yAxis' => [
				 'title' => ['text' => 'Acceleration (m/s2)']
				],
			    
				'series' => [
					[
						'name' => 'x',
						'data' =>''
					],
					[
						'name' => 'y',
						'data' =>''
					],
					[
						'name' => 'z',
						'data' =>''
					],
				], 
		   ]
		])?>
	</div>
	<div id="gyro">
		<?= Highcharts::widget([
		   'options' => [
				'title' => ['text' => 'Gyroscope'],
				'chart' => [
				  'renderTo' => 'gyro',
				  'zoomType'=> 'x',
				  'type'=> 'spline',
				],   

				'xAxis' => [
					 'type' => 'datetime',
			
				],

				'yAxis' => [
				 'title' => ['text' => 'Angular velocity (rad/s)']
				],
			    
				'series' => [
					[
						'name' => 'x',
						'data' =>''
					],
					[
						'name' => 'y',
						'data' =>''
					],
					[
						'name' => 'z',
						'data' =>''
					],
				], 
		   ]
		])?>
	</div>
	
	<div id="vibration">
		<?= Highcharts::widget([
		   'options' => [
				'title' => ['text' => 'Vibration'],
				'chart' => [
				  'renderTo' => 'vibration',
				  'zoomType'=> 'x',
				  'type'=> 'spline',
				],   
				'rangeSelector' => [
					'selected' => 2
				],
				'xAxis' => [
					 'type' => 'datetime',
				],

				'yAxis' => [
				 'title' => ['text' => 'Force (g)']
				],
			    
				'series' => [
					[
						'name' => 'IN',
						'data' =>'',
						'lineWidth' => 0,
						'marker' => [
							'enabled'	=> True,
							'radius'	=> 5
						],
						'tooltip' => [
							'valueDecimals' => 2
						],
						'states' => [
							'hover'=> [
								'lineWidthPlus' => 0
							]
						]
					],
				], 
			
		   ]
		])?>
	</div>
	
</div>

<?php

$this->registerJS("

		Highcharts.setOptions({
			global: {
				useUTC: false
			}
		});

		var acc = $('#acc').highcharts();
			
		var gyro = $('#gyro').highcharts();
		
		var vibration = $('#vibration').highcharts();
		
		var request = $request;
		
		//read from DB
		var jsonPath = 'acc?id=$model->id';
		
		var pathJSON = {
			acc : {
				request : '/storage/json/acc.json',
				db 		: 'acc?id=$model->id'
			},
			gps : {
				request : '/storage/json/gps.json',
				db 		: 'gps?id=$model->id'
			},
			vibration: {
				request : '/storage/json/vib.json',
				db 		: 'vibration?id=$model->id'
			}
		};
		
		
		//real time update data
		if(request) setInterval(requestData, 1000);
		
		
		init();
		var x =[], y=[], z=[], Gx=[], Gy=[], Gz=[], vib=[];
		var accStart=0;
		
		function requestData(){
			//acc
			$.getJSON(pathJSON.acc.request, function (data) {

				var dataLen = data.length;
				var xLen = accStart;
				if (xLen < dataLen){
					for (k=xLen; k< dataLen; k=k+4){
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
					
					accStart = dataLen;
					
				}
			});	
			
			//vibration
			$.getJSON(pathJSON.vibration.request, function (data) {
				var dataLen = data.length;
				var vibLen = vib.length;
				if (vibLen < dataLen){
					for(k=vibLen;k<data.length;k++){
				
						//convert to UNIX
						var ts = new Date(data[k][0]).getTime();
						
						vib.push([ts,data[k][1]]);			
					}
					vibration.series[0].update({
					//pointStart: newSeries[0].pointStart,
						data: vib
					}, true);
				}
			});


		
		}


		function init(){
			$.getJSON(pathJSON.acc.db, function (data) {
				
				for(k=0;k<data.length;k++){
			
					//convert to UNIX
					var ts = new Date(data[k][0]).getTime();
					
					x.push([ts,data[k][1]]);
					y.push([ts,data[k][2]]);
					z.push([ts,data[k][3]]);
					Gx.push([ts,data[k][4]]);
					Gy.push([ts,data[k][5]]);
					Gz.push([ts,data[k][6]]);
					
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
				
				accStart = x.length;
				console.log('Data len: '+data.length);
				console.log('X len: '+x.length);
				
			});
			
			$.getJSON(pathJSON.vibration.db, function (data) {
				for(k=0;k<data.length;k++){
			
					//convert to UNIX
					var ts = new Date(data[k][0]).getTime();
					
					vib.push([ts,data[k][1]]);			
				}
				vibration.series[0].update({
				//pointStart: newSeries[0].pointStart,
					data: vib
				}, true);
			});
			
			
		}
		
	");
	
?>
