<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model common\models\Test */

$this->title = Yii::t('frontend', 'Create Test');
$this->params['breadcrumbs'][] = ['label' => Yii::t('frontend', 'Tests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="test-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'vechicles' => $vechicles
    ]) ?>

</div>
