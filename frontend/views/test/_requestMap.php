<?php
use yii\helpers\Html;

//load google chart js
use common\assets\ChartGoogle;
ChartGoogle::register($this);
?>

<div class="2015-btn">zmien</div>
<div id="map"></div>


<?php

$this->registerJS("

  google.load('visualization', '1', {packages:['map'], 'callback': drawMap});
  //google.setOnLoadCallback(drawMap);
  var mapChart;



  function drawMap() {
    var data = google.visualization.arrayToDataTable([
      ['Lat', 'Long', 'Name'],
      [54.539269, 17.747169, 'Work'],
    ]);

  var options = {
    mapType: 'styledMap',
    showTooltip: false,
    showInfoWindow: true,
    maps: {
       // Your custom mapTypeId holding custom map styles.
       styledMap: {
         name: 'Styled Map', // This name will be displayed in the map type control.
         styles: [
           {featureType: 'poi.attraction',
            stylers: [{color: '#fce8b2'}]
           },
           {featureType: 'road.highway',
            stylers: [{hue: '#0277bd'}, {saturation: -50}]
           },
           {featureType: 'road.highway',
            elementType: 'labels.icon',
            stylers: [{hue: '#000'}, {saturation: 100}, {lightness: 50}]
           },
           {featureType: 'landscape',
            stylers: [{hue: '#259b24'}, {saturation: 10}, {lightness: -22}]
           }
     ]}}
  };

  mapChart = new google.visualization.Map(document.getElementById('map'));

  mapChart.draw(data, options);
  };

  $(document).ready(function(){



  //On button click, load new data
      $('.2015-btn').click(function() {
          console.log('click');
          var data = google.visualization.arrayToDataTable([
            ['Lat', 'Long', 'Name'],
            [54.539269, 17.747169, 'Work'],
            [54.537508, 17.750857, 'University'],
            [54.532856, 17.758447, 'time']


          ]);

          var options = {
            mapType: 'styledMap',
            showTooltip: true,
            showInfoWindow: true,
            maps: {
             // Your custom mapTypeId holding custom map styles.
             styledMap: {
               name: 'Styled Map', // This name will be displayed in the map type control.
               styles: [
                 {featureType: 'poi.attraction',
                  stylers: [{color: '#fce8b2'}]
                 },
                 {featureType: 'road.highway',
                  stylers: [{hue: '#0277bd'}, {saturation: -50}]
                 },
                 {featureType: 'road.highway',
                  elementType: 'labels.icon',
                  stylers: [{hue: '#000'}, {saturation: 100}, {lightness: 50}]
                 },
                 {featureType: 'landscape',
                  stylers: [{hue: '#259b24'}, {saturation: 10}, {lightness: -22}]
                 }
           ]}}

          };
          mapChart.draw(data, options);

      });
  });

");

 ?>
