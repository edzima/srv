<?php


use scotthuangzl\googlechart\GoogleChart;

?>
<div class="col-sm-12">
<?php
if ($position){

  echo '<h4 class="text-center">'.Yii::t('frontend', 'Travel distance').'</h4>';

  echo GoogleChart::widget( array('visualization' => 'Map',
            'packages'=>'map',//default is corechart
            'loadVersion'=>1,//default is 1.  As for Calendar, you need change to 1.1
            'data' => $position,
            'options' => array(
              'title' => 'Position',
              'showTip'=>true,
              'mapType' => 'styledMap',
              'maps' => [
                   // Your custom mapTypeId holding custom map styles.
                   'styledMap' => [
                     'name' => 'Styled Map', // This name will be displayed in the map type control.
                     'styles' => [
                       ['featureType' => 'poi.attraction',
                        'stylers' => ['color' => '#fce8b2']
                       ],
                       ['featureType' => 'road.highway',
                        'stylers' => [['hue'=> '#0277bd'], ['saturation' => '-50']]
                       ],
                       ['featureType' => 'road.highway',
                        'elementType' => 'labels.icon',
                        'stylers' => [['hue' => '#000'], ['saturation' => '100'], ['lightness'=> '50']]
                       ],
                       ['featureType' => 'landscape',
                        'stylers' => [['hue'=> '#259b24'], ['saturation' => '10'], ['lightness' => '-22']]
                       ]
                 ]]]
            )
          )
        );
}

?>

</div>
