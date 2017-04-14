<?php
use yii\helpers\Html;
/* @var $this yii\web\View */

$this->title = Yii::$app->name;
?>
<div class="site-index">
    <div class="jumbotron">
        <h1><?= $this->title?></h1>

        <p class="lead"><?=Yii::t('frontend', 'Start tests your vechicles')?>.</p>

        <p>
            <?= Html::a(Yii::t('frontend', 'Create Test'), ['/test/create'], ['class' => 'btn btn-success']) ?>
            <?= Html::a(Yii::t('frontend', 'Vehicles'), ['/vechicle/index'], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('frontend', 'Tests'), ['/test/index'], ['class' => 'btn btn-danger']) ?>
        </p>
    </div>
</div>
