<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Registry */

$this->title = $model->orderIdorder->hybridIdHybr->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="registry-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php
    if(substr($model->orderIdorder->hybridIdHybr->variety, 0, 1) == "T"){
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'quantity',
                'numRow',
                'LUser'
            ],
        ]);
    }else{
        echo DetailView::widget([
            'model' => $model,
            'attributes' => [
                'fruitsCount',
                'quantity',
                'quantity2',
                'LUser'
            ],
        ]);
    }
    ?>

</div>
