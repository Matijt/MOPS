<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\History */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'History',
]) . $model->idHistory;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idHistory, 'url' => ['view', 'id' => $model->idHistory]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
