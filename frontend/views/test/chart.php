<?php
use yii\helpers\Html;

$this->title = 'Test: '.$model->id;

$this->params['breadcrumbs'][] = $this->title;

?>
<div class="col-sm-12">
	
	<div id="container" style="height: 400px; min-width: 310px"></div>	
	<div id="speed" style="height: 400px; min-width: 310px"></div>	
	<div id="acc" style="height: 400px; min-width: 310px"></div>	
</div>

<?php


	$this->registerJs("
$(function () {
	
	var speedChart;
	
	var accChart;
	
//	setInterval(requestData, 300);
	
	
	function requestData(){
		$.getJSON('acc?testID=$model->id', function (data) {
			accChart.series[0].update({
			//pointStart: newSeries[0].pointStart,
			data: data
		}, true);
			
		});
	}
	
	
    $(document).ready(function () {

	Highcharts.setOptions({
		global: {
			useUTC: false
		}
	});

	/*
    $(function () {
		$.getJSON('speed', function (data) {

			Highcharts.chart('speed', {
				chart: {
					zoomType: 'x'
				},
				
				events: {
                    load: function () {

                        // set up the updating of the chart each second
                        var series = this.series[0];
                        setInterval(function () {
							$.getJSON('speed', function (data) {
								console.log('jest');

							});				
                        }, 1000);
                    }
                },
				
				title: {
					text: 'Speed over ground KM/H'
				},
				subtitle: {
					text: document.ontouchstart === undefined ?
							'Click and drag in the plot area to zoom in' : 'Pinch the chart to zoom in'
				},
				xAxis: {
					type: 'datetime',
					tickPixelInterval: 150
				},
				yAxis: {
					title: {
						text: 'Exchange rate'
					}
				},
				legend: {
					enabled: false
				},
				plotOptions: {
					area: {
						fillColor: {
							linearGradient: {
								x1: 0,
								y1: 0,
								x2: 0,
								y2: 1
							},
							stops: [
								[0, Highcharts.getOptions().colors[0]],
								[1, Highcharts.Color(Highcharts.getOptions().colors[0]).setOpacity(0).get('rgba')]
							]
						},
						marker: {
							radius: 2
						},
						lineWidth: 1,
						states: {
							hover: {
								lineWidth: 1
							}
						},
						threshold: null
					}
				},

				series: [{
					type: 'area',
					name: 'Speed km/h',
					data: data
				}]
			});
		});
	});
	*/

	var options = {
        chart: {
				zoomType: 'x',
            renderTo: 'speed',
        },
		xAxis: {
			type: 'datetime',
			tickPixelInterval: 150
		},
		title: {
			text: 'Speed over ground KM/H'
		},
		
		yAxis: {
			title: {
				text: 'Acc'
			}
		},
		
		series: [{}],  

    };

    $.getJSON('speed?testID=$model->id', function(data) {
        options.series[0].data = data;
       speedChart = new Highcharts.Chart(options);
    });
	
	var accOptions = {
        chart: {
			zoomType: 'x',
            renderTo: 'acc',
            type: 'spline',
        },
		xAxis: {
			type: 'datetime',
			tickPixelInterval: 150
		},
		yAxis: {
		},
		title: {
			text: 'Accelerometer and Gyroscope'
		},
		series: [{}],  
    };

    $.getJSON('/img/testArr.json', function(data) {
        accOptions.series[0].data = data;
       accChart = new Highcharts.Chart(accOptions);
    });
	

	$('#container').highcharts({
            chart: {
                type: 'spline',
                animation: Highcharts.svg, // don't animate in old IE
                marginRight: 10,
                events: {
                    load: function () {

                        // set up the updating of the chart each second
                        var series = this.series[0];
                        setInterval(function () {
							/*$.getJSON('speed', function (data) {

					
								  var x = data[0].time, // current time
								y = parseFloat(data[0].speed);
								
								series.addPoint([x, y], true, true);
							
								 //console.log(data[i].time);
					
								 //series.addPoint([data[i].time,data[i].speed],true,true);
								
							

							});
*/						
                        }, 1000);
                    }
                }
            },
            title: {
                text: 'Live random data'
            },
 
			xAxis: { type: 'datetime' },
            legend: {
                enabled: false
            },
            exporting: {
                enabled: false
            },
            series: [{
                name: 'Random data',
                data: (function () {
                    // generate an array of random data
                    var data = [],
                        time = (new Date()).getTime(),
                        i;

                    for (i = -19; i <= 0; i += 1) {
                        data.push({
                            x: time + i * 1000,
                            y: Math.random()
                        });
                    }
                    return data;
                }())
            }]
        });
    });
});




");
	
	
 ?>