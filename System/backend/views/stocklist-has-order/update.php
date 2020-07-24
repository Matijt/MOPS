<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\StocklistHasOrder */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Stocklist Has Order',
]) . $model->idstocklist_has_order;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklist Has Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idstocklist_has_order, 'url' => ['view', 'idstocklist_has_order' => $model->idstocklist_has_order, 'order_idorder' => $model->order_idorder]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="stocklist-has-order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
