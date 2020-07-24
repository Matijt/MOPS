<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\NumcropHasCompartment */

$this->title = Yii::t('app', 'Create Numcrop Has Compartment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Numcrop Has Compartments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="numcrop-has-compartment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
