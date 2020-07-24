<?php

use yii\helpers\Html;
use kartik\grid\GridView;
use \yii\helpers\Url;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\RegistrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Registries');
$this->params['breadcrumbs'][] = $this->title;

$users = \backend\models\Order::find();
?>
<div class="registry-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?=Html::button(Yii::t('app', 'Create Registry'), ['value' => Url::to('index.php?r=registry/create'), 'class' => 'modalButtonCreate']);?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            [
                'attribute'=>'order_idorder',
                'value'=>'orderIdorder.fullname',
                'filterType'=>GridView::FILTER_SELECT2,
                    'filter'=>\yii\helpers\ArrayHelper::map($users->andFilterWhere(['=', 'order.delete',0])
                        ->andFilterWhere(['=', 'order.state', 'Active'])
                        ->andFilterWhere(['!=', 'order.sowingDateF', '1970-01-01'])
                        ->andFilterWhere(['=', 'order.trial_id', 1])->all(), 'idorder', 'fullName'),
                'filterWidgetOptions'=>['pluginOptions'=>['allowClear'=>true],],
                'filterInputOptions'=>['placeholder'=>'Select the order'],
            ],
            'numRow',
            'quantity',
            'LUser',

            ['class' => 'yii\grid\ActionColumn',
                'template' => array_key_exists('Administrator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) || array_key_exists('Estimator Helper', \Yii::$app->authManager->getRolesByUser(Yii::$app->user->id)) ?
                    '{view}{update}':'{view}',
                'buttons' => [
                    'view' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-eye-open"></span>', ['value' => Url::to('index.php?r=registry%2Fview&id='.$model->idregistry), 'class' => 'modalButtonView'], [
                            'title' => Yii::t('app', 'View'),
                        ]);
                    },

                    'update' => function ($url, $model) {
                        return Html::button('<span class="glyphicon glyphicon-pencil"></span>', ['value' => Url::to('index.php?r=registry%2Fupdate&id='.$model->idregistry), 'class' => 'modalButtonEdit'], [
                            'title' => Yii::t('app', 'Update'),
                        ]);
                    },
                ],
            ],
        ],
    ]); ?>
</div>
