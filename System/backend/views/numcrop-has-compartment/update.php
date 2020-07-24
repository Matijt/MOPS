<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\NumcropHasCompartment */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Surfaces planning',
]) . "crop #: ".$model->numcrop_cropnum." compartment: ".$model->compartmentIdCompartment->compNum." crop: ".$model->cropIdcrops->crop;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Surfaces planning'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => "crop #: ".$model->numcrop_cropnum." compartment: ".$model->compartmentIdCompartment->compNum." crop: ".$model->cropIdcrops->crop, 'url' => ['view', 'numcrop_cropnum' => $model->numcrop_cropnum, 'compartment_idCompartment' => $model->compartment_idCompartment]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="numcrop-has-compartment-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
