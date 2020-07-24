<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Mother */

$this->title = $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Female'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mother-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->idMother], ['class' => 'btn btn-primary']) ?>
        <?php Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->idMother], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
        <?php Html::a(Yii::t('app', 'Create'), ['create', 'id' => $model->idMother], ['class' => 'btn btn-success']) ?>
        <?php Html::a(Yii::t('app', 'View all'), ['index', 'id' => $model->idMother], ['class' => 'btn btn-info']) ?>
    </p>

    <?php
    $atr = [
        'variety',
    ];
    if ($model->steril == 100){
      $atr[] = [
          'attribute' => 'steril',
          'value' => 'No',
      ];
    }else{
        $atr[] = [
            'attribute' => 'steril',
            'value' => 'Yes',
        ];
    }
    $atr =array_merge($atr,
        [
            [
                'attribute' => 'germination',
                'value' => function($model){
                    return Facil::limitarDecimales($model->germination);
                }
            ],
            [
                'attribute' => 'tsw',
                'value' => function($model){
                    return Facil::limitarDecimales($model->tsw);
                }
            ],
            [
                'attribute' => 'gP',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gP);
                }
            ],
            'remarks:ntext',
    ]);

    echo DetailView::widget([
        'model' => $model,
        'attributes' => $atr,
    ]) ?>

</div>
