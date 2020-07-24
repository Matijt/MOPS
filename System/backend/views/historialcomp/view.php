<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Historialcomp */

$this->title = $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Historialcomps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historialcomp-view">

    <h1><?= Html::encode($this->title) ?></h1>
    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
                'crop',
            'title',
            'content:ntext',
            [
                'attribute' => 'date',
                'value' => date('d-m-Y', strtotime($model->date)),
            ],
            'compartmentIdCompartment.compNum',
        ],
    ]) ?>

</div>
