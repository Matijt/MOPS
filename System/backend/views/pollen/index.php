<?php

use yii\helpers\Html;
use yii\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;
use yii\helpers\Url;
use yii\widgets\ActiveForm;


/* @var $this yii\web\View */
/* @var $searchModel backend\models\PollenSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Pollen');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pollen-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-lg-4">
            <p>
                <?= Html::button(Yii::t('app', 'Create Pollen'), ['value' => Url::to('index.php?r=pollen/create'), 'class' => 'btn btn-success modalButtonCreate']) ?>
            </p>
        </div>
        <?php $form = ActiveForm::begin(['options' => ['enctype' => 'multipart/form-data']]);
         ?>
        <div class="col-lg-4">
            <p>
                <?= $form->field($model, 'file')->fileInput() ?>
            </p>
        </div>
        <div class="col-lg-4 form-group">
            <p>
                <?= Html::submitButton(Yii::t('app', 'Upload info'), ['class' => 'btn btn-primary']) ?>
            </p>
        </div>

        <?php ActiveForm::end()?>
    </div>


    <?php
    $gridColumns = [

        'harvestWeek',
        'harvestDate',
        'harvestMl',
        'useWeek',
        'useMl',
    ];

    echo ExportMenu::widget([
        'dataProvider' => $dataProvider,
        'columns' => $gridColumns,
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
        ],
        'filename' => 'Pollen_'.date('d-m-Y'),
    ])
    ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'harvestWeek',
            [
                'attribute' => 'harvestDate',
                'value' => 'harvestDate',
                'format' => ['date', 'php:d/m/Y'],
                'filter' => DateRangePicker::widget([
                    'model'=>$searchModel,
                    'attribute'=>'harvestDate',
                    'convertFormat'=>true,
                    'pluginOptions'=>[
//                        'timePicker'=>true,
                        //                      'timePickerIncrement'=>30,
                        'locale'=>[
                            'format'=>'d-m-Y'
                        ]
                    ]
                ]),
            ],
            'harvestMl',
        //    'useWeek',
            // 'useMl',
            // 'youHaveMl',
            // 'order_idorder',

            ['class' => 'yii\grid\ActionColumn',
                'template' => '{view}{update}{delete}',
                'buttons' => [
                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=pollen%2Fupdate&id='.$model->idpollen;
                        }else{
                            $edit = 'index.php?r=pollen%2Fupdate&id='.$model->idpollen."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit btn btn-primary'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=pollen/delete&id='.$model->idpollen;
                        }else{
                            $edit = 'index.php?r=pollen/delete&id='.$model->idpollen."&page=".$_GET['page'];
                        }
                        return Html::a('<span class="glyphicon glyphicon-trash"></span>', $edit, [
                            'data-method' => 'POST',
                            'title' => Yii::t('app', 'Update'),
                            'data-confirm' => "Are you sure to delete this item?",
                            'role' => 'button',
                            'class' => 'modalButtonDelete',
                        ]);
                    }
                ],
            ],
        ],
    ]); ?>
</div>

<style>
    .glyphicon-eye-open{

        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 0;
        font-size: 14px;
        font-weight: normal;
        line-height: 1.42857143;
        text-align: center;
        white-space: nowrap;
        vertical-align: middle;
        -ms-touch-action: manipulation;
        touch-action: manipulation;
        cursor: pointer;
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none;
        background-image: none;
        border: 1px solid transparent;
        border-radius: 4px;

        color: #fff;
        background-color: #337ab7;
        border-color: #2e6da4;

    }
</style>
