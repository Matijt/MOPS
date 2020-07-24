<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Trial */

$this->title = Yii::t('app', 'Create Trial');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="trial-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsorder' => (empty($modelsorder)) ? [new \backend\models\Order()] : $modelsorder
    ]) ?>

</div>
