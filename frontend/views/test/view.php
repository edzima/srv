<?php

use yii\helpers\Html;
use yii\bootstrap\Alert;


/* @var $this yii\web\View */
/* @var $model common\models\Test */

$this->title = Yii::t('frontend', 'Details Test: {id}',['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Tests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>

<?php 
	if (Yii::$app->session->hasFlash('stopTest')) echo Alert::widget([
	   'options' => ['class' => 'alert-info'],
	   'body' => Yii::$app->session->getFlash('stopTest'),
	]);

?>


 
<div class="test-view">

    <h1><?= Html::encode($this->title) ?></h1>


    <p>
        <?= Html::a(Yii::t('frontend', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('frontend', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>
	
	

	
	<?= $this->render('_chart', [
        'model' => $model,
		'request' => 0 
    ]) ?>
	

</div>


