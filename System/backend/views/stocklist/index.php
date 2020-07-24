<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\Url;
use yii\widgets\ActiveForm;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocklistSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Stocklists');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-lg-4">
            <p>
                <?php
                if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
                    Html::button(Yii::t('app', 'Create Stocklist'), ['value' => 'index.php?r=stocklist/create','class' => 'modalButtonCreate']);
} ?>
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
    </div>

    <?php ActiveForm::end()?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function($model){
            if($model->status == "In Stock"){
                return ['class' => 'success', 'style' => 'color: green;'];
            }else if($model->status == "Shipped"){
                return ['style' => 'color: yellow; background-color: #f1ae50'];
            }else{
            return ['class' => 'danger', 'style' => 'color: red;'];
            }
        },
        'columns' => [

//            'idstocklist',
            'harvestNumber',
            'harvestDate',
            'numberOfFruitsHarvested',
            // 'cleaningDate',
            // 'wetSeedWeight',
            // 'drySeedWeight',
            // 'avgWeightOfSeedPF',
            // 'numberOfBags',
            // 'cartonNo',
            // 'shipmentDate',
            // 'packingListDescription',
            // 'remarksSeeds',
            // 'destroyed',
            // 'moisture',
            // 'tsw',
            // 'eol',
            // 'status',

            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}{delete}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=stocklist%2Fview&id='.$model->idstocklist), 'class' => 'modalButtonView btn btn-primary'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=stocklist%2Fupdate&id='.$model->idstocklist;
                        }else{
                            $edit = 'index.php?r=stocklist%2Fupdate&id='.$model->idstocklist."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit btn btn-primary'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },

                    'delete' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=stocklist/delete&id='.$model->idstocklist;
                        }else{
                            $edit = 'index.php?r=stocklist/delete&id='.$model->idstocklist."&page=".$_GET['page'];
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
