<?php
use yii\helpers\Html;

use miloschuman\highcharts\Highcharts;


?>
<div class="col-md-12">


	<div id="speed">
	<?= Highcharts::widget([
			 'options' => [
				'title' => ['text' => Yii::t('frontend', 'Speed')],
				'chart' => [
					'renderTo' => 'speed',
					'zoomType'=> 'x',
					'type'=> 'spline',
					'animation' => 'Highcharts.svg',
				],

				'xAxis' => [
					 'type' => 'datetime',
				],

				'tooltip' => ['shared' => true],

				'yAxis' => [
					//primary yAxis
					[
						'labels' => ['format'=>'{value} km/h'],
						'title' => ['text' => Yii::t('frontend', 'Speed (km/h)')]

					],
					[
						'labels' => ['format' =>'{value} m'],
						'title' => ['text' => Yii::t('frontend','Braking distance')],
						'opposite' => True,

					],

				],

				'series' => [
					[
						'name' => Yii::t('frontend', 'Speed over ground'),
						'data' =>'',
						'tooltip' => ['valueSuffix' => ' km/h'],

					],
					[
						'name' => Yii::t('frontend','Braking distance'),
						'data' =>'',
						'type' => 'column',
						'yAxis' => 1,
						'tooltip' => ['valueSuffix' => ' m'],
						'pointWidth' => 5,
					]
				],
			 ]
		])?>
	</div>

	<div id="acc">
	<?= Highcharts::widget([
		   'options' => [
				'title' => ['text' => Yii::t('frontend', 'Accelerometer')],
				'chart' => [
				  'renderTo' => 'acc',
				  'zoomType'=> 'xy',
				  'type'=> 'spline',
				  'animation' => 'Highcharts.svg',
				],

				'xAxis' => [
					 'type' => 'datetime',

				],

				'yAxis' => [
					[ // Primary yAxis
						'title'=> [
								'text'=> 'Acceleration',
						],
						'labels' => [
								'format' => '{value} m/s2',
						],

						'opposite' => True


					],
					[ // Secondary yAxis
							'gridLineWidth'=> 0,
							'title'=> [
									'text' =>'Speed',
							],
							'labels' => [
									'format' => '{value} km/h',
							]

					]
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
					[
						'name' => 'speed',
						'data' =>''
					],
				],
		   ]
		])?>
	</div>
	<div id="gyro">
		<?= Highcharts::widget([
		   'options' => [
				'title' => ['text' => Yii::t('frontend', 'Gyroscope')],
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
				'title' => ['text' => Yii::t('frontend','Vibrations')],
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
                    'title' => ['text' => 'Force (g)'],
                    'min' => 0.2,
				],

				'series' => [
					[
						'name' => 'IN',
						'data' =>'',
                        // points marker
						'lineWidth' => 0,
						'marker' => [
							'enabled'	=> True,
							'radius'	=> 2
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
                    [
                        'name' => 'OUT',
                        'data' =>'',
                        // points marker
                        'lineWidth' => 0,
                        'marker' => [
                            'enabled'	=> True,
                            'radius'	=> 2
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

		function unselectByClick(){
			console.log('click speed');
		}

		Highcharts.setOptions({
			global: {
				useUTC: false
			}
		});

		var acc = $('#acc').highcharts();
		var gyro = $('#gyro').highcharts();
		var speedChart = $('#speed').highcharts();
		var vibration = $('#vibration').highcharts();

		speedChart.selection;

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
				db 		: 'gps?id=$model->id',
				brakingDistance : 'brakingdistance?id=$model->id'
			},
			vibration: {
				request : '/storage/json/vib.json',
				db 		: 'vibration?id=$model->id'
			}

		};


		//real time update data
		if(request) setInterval(requestData, 1000);


		init();
		var x =[], y=[], z=[], Gx=[], Gy=[], Gz=[], vib=[], vibOut=[], speed=[], distance=[];
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
					//	var ts = new Date(data[k][0]).getTime();

						//vib.push([ts,data[k][1]]);
					}
					vibration.series[0].update({
					//pointStart: newSeries[0].pointStart,
						data: vib
					}, true);
				}
			});



		}


		function init(){
			//acc and gyro get data
			$.getJSON(pathJSON.acc.db, function (data) {

				for(k=0;k<data.length;k++){

					//convert to UNIX
					//var ts = new Date(data[k][0]).getTime();

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

				accStart = x.length;
				console.log('Data len: '+data.length);
				console.log('X len: '+x.length);

			});

			//vibration get data
			$.getJSON(pathJSON.vibration.db, function (data) {
				for(k=0;k<data.length;k++){

					//convert to UNIX
                //    console.log(data[k].time);
					var ts = new Date(data[k].time).getTime();
                    if(data[k].sensor_id==1)    vib.push([ts,parseFloat(data[k].peakForce)]);
                    else vibOut.push([ts,parseFloat(data[k].peakForce)]);

					//vib.push([ts,data[k][1]]);
				}
				vibration.series[0].update({
				//pointStart: newSeries[0].pointStart,
					data: vib
				}, true);

                vibration.series[1].update({
                    data: vibOut
                },true);
			});

			//speed get data
			$.getJSON(pathJSON.gps.db, function (data) {
				for(k=0;k<data.length;k++){
					//convert to UNIX
					var ts = new Date(data[k][0]).getTime();

					speed.push([ts,data[k][1]]);
				}
				console.log(ts);

				speedChart.series[0].update({
				//pointStart: newSeries[0].pointStart,
					data: speed
				}, true);

				acc.series[3].update({
				//pointStart: newSeries[0].pointStart,
					data: speed,
					yAxis:1
				}, true);
			});


			//speed get data
			$.getJSON(pathJSON.gps.brakingDistance, function (data) {
				for(k=0;k<data.length;k++){
					//convert to UNIX
					//var ts = new Date(data[k].).getTime();
					var ts = new Date(data[k].time).getTime();

					distance.push([ts, data[k].distance]);

				}

				speedChart.series[1].update({
					data: distance,
				}, true);


			});


		}




	");

?>
