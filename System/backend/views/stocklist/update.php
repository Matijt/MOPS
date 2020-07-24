<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Stocklist */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Stocklist',
]) . $model->harvestNumber;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idstocklist, 'url' => ['view', 'id' => $model->idstocklist]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="stocklist-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
