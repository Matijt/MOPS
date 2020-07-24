<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use dosamigos\datepicker\DatePicker;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\GerminationSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Germinations');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="germination-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

          //  'idGermination',
            'maleOrfemale',
            'variety',
            'description:ntext',
            [
                'attribute' => 'updatedAt',
                'value' => 'updatedAt',
                'format' => ['date', 'php:d/m/Y'],
                'filter' => DatePicker::widget([
                    'model' => $searchModel,
                    'attribute' => 'updatedAt',
                    'clientOptions' => [
                        'autoclose' => true,
                        'format' => 'dd-mm-yyyy'
                    ]
                ])
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
