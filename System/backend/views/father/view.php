<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Father */

$this->title = $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Males'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="father-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'variety',
            'steril',
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
            'remarks:ntext',
        ],
    ]) ?>

</div>
