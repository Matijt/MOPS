<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Compartment */

$this->title = Yii::t('app', 'Create Compartment');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Compartments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compartment-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
