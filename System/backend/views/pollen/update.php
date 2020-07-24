<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\backend\models\Pollen */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Pollen',
]) . ' ' . $model->orderIdorder->fullName;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pollen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orderIdorder->fullName, 'url' => ['view', 'idpollen' => $model->idpollen, 'order_idorder' => $model->order_idorder]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="pollen-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
