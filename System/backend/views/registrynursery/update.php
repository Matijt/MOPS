<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Registrynursery */

$this->title = 'Update Nursery Registration: ' . $model->orderIdorder->hybridIdHybr->variety;
$this->params['breadcrumbs'][] = ['label' => 'Registrynurseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="registrynursery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
