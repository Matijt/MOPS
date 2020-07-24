<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel backend\models\CompartmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app', 'Compartments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compartment-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [

            'compNum',
            'rowsNum',
            'grossSurface',
            'netSurface',
            // 'grossLength',
            // 'netLength',
            // 'width',
            // 'remarks:ntext',

        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
