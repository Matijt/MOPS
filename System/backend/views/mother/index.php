<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\helpers\Url;
use backend\codigo\Facil;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\MotherSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Females');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mother-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?php
        if (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) {
            echo Html::button(Yii::t('app', 'Create Female'), ['value' => 'index.php?r=mother/create','class' => 'modalButtonCreate']);
        } ?>
    </p>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            'variety',
            [
                'attribute' => 'germination',
                'value' => function($model){
                    return Facil::limitarDecimales($model->germination);
                }
            ],
//            'tsw',
            [
                'attribute' => 'gP',
                'value' => function($model){
                    return Facil::limitarDecimales($model->gP);
                }
            ],
   //          'ratio',
            // 'remarks:ntext',


            ['class' => 'yii\grid\ActionColumn',
                'template' => (array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Production', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id))) ?
                    '{view}{update}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=mother%2Fview&id='.$model->idMother), 'class' => 'modalButtonView btn btn-primary'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        $actual_link = (isset($_SERVER['HTTPS']) ? "https" : "http") . "://$_SERVER[HTTP_HOST]$_SERVER[REQUEST_URI]";
                        if (!(strpos($actual_link, 'page') !== false)){
                            $edit = 'index.php?r=mother%2Fupdate&id='.$model->idMother;
                        }else{
                            $edit = 'index.php?r=mother%2Fupdate&id='.$model->idMother."&page=".$_GET['page'];
                        }
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to($edit), 'class' => 'modalButtonEdit btn btn-primary'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>

</div>
