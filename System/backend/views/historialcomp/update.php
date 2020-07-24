<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Historialcomp */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Historialcomp',
]) . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Historialcomps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->idHistorialcomp]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="historialcomp-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
