<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */

if(isset($_GET['name'])){
$name = $_GET['name'];
}
$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Order',
]) . $model->ReqDeliveryDate." ".$model->hybridIdHybr->variety;

$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formf', [
        'model' => $model,
    ]) ?>

</div>
