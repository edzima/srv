<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Test */

$this->title = Yii::t('frontend', 'Test: {id}',['id' => $model->id]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Tests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-create">

    <h1><?= Html::encode($this->title) ?></h1>

	<div class="form-group"><?= Html::a(Yii::t('app','Stop'), ['stop','id' =>$model->id], ['class'=>'btn btn-danger', 'style' => 'padding-right:10px;']) ?> </div>

    <?= $this->render('_chart', [
        'model' => $model,
		    'request' => 1
    ]) ?>


</div>
