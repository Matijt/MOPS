<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\StocklistHasOrder */
/* @var $modelSL backend\models\Stocklist */

$this->title = Yii::t('app', 'Create Stocklist Has Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklist Has Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-has-order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelSL' => $modelSL,
    ]) ?>

</div>
