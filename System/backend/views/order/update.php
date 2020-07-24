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
if(isset($name)){
    $this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'state'), 'url' => [$name."index"]];
    $this->params['breadcrumbs'][] = ['label' => $model->ReqDeliveryDate." ".$model->hybridIdHybr->variety, 'url' => ['view', 'id' => $model->idorder, 'name' => $name]];
}else{
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->ReqDeliveryDate." ".$model->hybridIdHybr->variety, 'url' => ['view', 'id' => $model->idorder]];
}
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="order-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
