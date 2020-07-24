<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Histcrop */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Histcrop',
]) . $model->idhistCrop;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Histcrops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idhistCrop, 'url' => ['view', 'id' => $model->idhistCrop]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="histcrop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
