<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Compartment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Compartment',
]) . $model->idCompartment;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Compartments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idCompartment, 'url' => ['view', 'id' => $model->idCompartment]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="compartment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
