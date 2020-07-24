<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\grid\GridView;
use kartik\export\ExportMenu;
use kartik\daterange\DateRangePicker;

/* @var $this yii\web\View */
/* @var $model backend\models\backend\models\Pollen */

$this->title = $model->orderIdorder->fullName;
$name = str_replace(':', '', $this->title);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Pollen'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;


if($model->harvestDate) {
    $model->harvestDate = date('d-m-Y', strtotime($model->harvestDate));
}
if ($model->useWeek) {
    $model->useWeek = date('d-m-Y', strtotime($model->useWeek));
}
?>

<div class="pollen-view">

    <div class="row">
        <div class="col-sm-12">
            <h2><?= Yii::t('app', 'Pollen').' '. Html::encode($this->title) ?></h2>
        </div>
    </div>

    <div class="row">
        <div class="col-lg-12">
<?php
    $gridColumn = [
        [
            'attribute' => 'orderIdorder.compartmentIdCompartment.compNum',
            'value' => 'orderIdorder.compartmentIdCompartment.compNum',
            'label' => 'Compartment'
        ],
        [
            'attribute' => 'orderIdorder.numCrop',
            'value' => 'orderIdorder.numCrop',
            'label' => 'Crop'
        ],
        [
            'attribute' => 'orderIdorder.hybridIdHybr.variety',
            'value' => 'orderIdorder.hybridIdHybr.variety',
        ],
        [
            'attribute' => 'orderIdorder.hybridIdHybr.fatherIdFather.variety',
            'value' => 'orderIdorder.hybridIdHybr.fatherIdFather.variety',
            'label' => 'Male'
        ],
        [
            'attribute' => 'orderIdorder.contractNumber',
            'value' => 'orderIdorder.contractNumber',
        ],
        'harvestWeek',
        'harvestDate',
        'harvestMl',
        'useWeek',
        'useMl',
    ];

    // Dividir la orden en los datos en la exportación.
    //

echo ExportMenu::widget([
    'dataProvider' => $dataProvider,
    'columns' => $gridColumn,
    'exportConfig' => [
        ExportMenu::FORMAT_TEXT => false,
        ExportMenu::FORMAT_CSV => false,
        ExportMenu::FORMAT_HTML => false,
    ],
    'filename' => $name
])
?>

<?= GridView::widget([
    'dataProvider' => $dataProvider,
    'filterModel' => $searchModel,
    'columns' => [
        [
            'attribute' => 'order_idorder',
            'value' => 'orderIdorder.fullname',
            'hidden' => true,
        ],
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
        'useWeek',
        'useMl',
    ],
]);
?>
        </div>
    </div>
</div>
