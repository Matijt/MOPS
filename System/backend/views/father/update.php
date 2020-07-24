<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Father */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Male',
]) . $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Males'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->variety, 'url' => ['view', 'id' => $model->idFather]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="father-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
