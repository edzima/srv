<?php

use backend\models\Log;
use backend\widgets\Menu;

/* @var $this \yii\web\View */
?>
<aside class="main-sidebar">
    <section class="sidebar">
        <?= Menu::widget([
            'options' => ['class' => 'sidebar-menu'],
            'items' => [

                [
                    'label' => Yii::t('backend', 'System'),
                    'options' => ['class' => 'header'],
                ],
                [
                    'label' => Yii::t('common', 'Users'),
                    'url' => ['/user/index'],
                    'icon' => '<i class="fa fa-users"></i>',
                    'visible' => Yii::$app->user->can('administrator'),
                ],

                [
                    'label' => 'Gii',
                    'url' => ['/gii'],
                    'icon' => '<i class="fa fa-cog"></i>',
                    'visible' => YII_ENV_DEV,
                ],
                [
                    'label' => 'Web shell',
                    'url' => ['/webshell'],
                    'icon' => '<i class="fa fa-terminal"></i>',
                    'visible' => Yii::$app->user->can('administrator'),
                ],
                ['label' => Yii::t('backend', 'File manager'), 'url' => ['/file-manager/index'], 'icon' => '<i class="fa fa-sitemap"></i>'],
                [
                    'label' => Yii::t('backend', 'DB manager'),
                    'url' => ['/db-manager/default/index'],
                    'icon' => '<i class="fa fa-database"></i>',
                    'visible' => Yii::$app->user->can('administrator'),
                ],
                ['label' => Yii::t('backend', 'Cache'), 'url' => ['/cache/index'], 'icon' => '<i class="fa fa-info"></i>'],
                [
                    'label' => Yii::t('backend', 'Logs'),
                    'url' => ['/log/index'],
                    'icon' => '<i class="fa fa-exclamation-triangle"></i>',
                    'badge' => Log::find()->count(), 'badgeBgClass' => 'label-danger',
                ],

            ],
        ]) ?>
    </section>
</aside>
