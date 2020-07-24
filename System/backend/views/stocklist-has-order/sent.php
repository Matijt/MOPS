<?php

use backend\models\Stocklist;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\export\ExportMenu;
use kartik\widgets\Select2;

/* @var $this yii\web\View */
/* @var $searchModel backend\models\StocklistHasOrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
/* @var $totals array() */

$this->title = Yii::t('app', 'Full Stocklist');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-has-order-index">




    <?php
    $form = ActiveForm::begin();
    $model = new \backend\models\Stocklist();
    $model->shipmentDate = $date;
    $keys = $dataProvider->keys;
    $count = 0;
    ?>

    <h1 style="text-align: center">Packing list</h1>
    <div class="row">
        <div class='col-sm-4'>


            <?=
            $form->field($model, 'shipmentDate')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(Stocklist::find()
                    ->andFilterWhere(['=', 'status', 'Shipped'])
                    ->all(),
                    'shipmentDate', 'fullDate'),
                'options' => [
                    'placeholder' => Yii::t('app', 'Choose Date'),
                    'onchange' => '
                    var url = window.location.href;
                    var rooturl = url.substr(0, (url.lastIndexOf(\'sent\') +  5));
                    var newurl = rooturl+"&date="' . '+($(this).val());
                    window.location.replace(newurl);
                    ',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]);
            ?>
        </div>
        <div class='col-sm-4'>
        </div>
        <div class='col-sm-4'>
            <br>
            <?php
            Html::button('Update date', ['id'=>"dialog", 'class' => 'btn btn-info btn-block',
                'onclick' =>'
                            window.location.replace(
                                "index.php?r=stocklist-has-order%2Fsent&date="'.'+($("#stocklist-shipmentdate").val())
                            );
                        ',
            ]);
            ?>
        </div>
    </div>
    <?php
    echo ExportMenu::widget([
        'dataProvider' => $provider,
        'columns' => [
            [
                'attribute' => 'cartonNo',
                'label' => 'Carton No',
            ],
            [
                'attribute' => 'variety',
                'label' => 'Variety',
            ],
            [
                'attribute' => 'lotNr',
                'label' => 'RZ Lot Number',
            ],
            [
                'attribute' => 'compCP',
                'label' => 'Compartment, Crop, Phase',
            ],
            [
                'attribute' => 'packLD',
                'label' => 'Description Seeds',
            ],
            [
                'attribute' => 'moisture',
                'label' => 'Moisture',
            ],
            [
                'attribute' => 'eol',
                'label' => 'EOL',
            ],
            [
                'attribute' => 'numOfBags',
                'label' => 'Bags',
            ],
            [
                'attribute' => "drySeedWeight",
                'label' => 'Net (KG)'
            ]
        ],
        'exportConfig' => [
            ExportMenu::FORMAT_TEXT => false,
            ExportMenu::FORMAT_CSV => false,
            ExportMenu::FORMAT_HTML => false,
//            ExportMenu::FORMAT_PDF => false,
        ],
        'showConfirmAlert' => false,
        'clearBuffers' => true,
        'filename' => 'Packing list_'.date('d-m-Y'),
    ])
    ?>
    <?= GridView::widget([
        'dataProvider' => $provider,
        'rowOptions' => function($model){
            if($model['eol'] == ""){
                return ['class' => 'success', 'style' => 'color: green;'];
            }else{
                return [];
            }
        },
        'columns' =>[
                        [
                            'attribute' => 'cartonNo',
                            'label' => 'Carton Number',
                        ],
                        [
                            'attribute' => 'variety',
                            'label' => 'Variety',
                        ],
                        [
                            'attribute' => 'lotNr',
                            'label' => 'RZ Lot Number',
                        ],
                        [
                            'attribute' => 'compCP',
                            'label' => 'Compartment, Crop, Phase',
                        ],
                        [
                            'attribute' => 'packLD',
                            'label' => 'Description Seeds',
                        ],
                        [
                            'attribute' => 'moisture',
                            'label' => 'Moisture',
                        ],
                        [
                            'attribute' => 'eol',
                            'label' => 'EOL',
                        ],
                        [
                            'attribute' => 'numOfBags',
                            'label' => 'Bags',
                        ],
                        [
                            'attribute' => "drySeedWeight",
                            'label' => 'Net (KG)'
                        ],
            ]
    ]); ?>


   <!-- <h1>Totals</h1>
    <div class="row">
        <?php
$i= 0;
$bags = 0;
$nets = 0;
        foreach($totals AS $total){
            if ($i == 0) {
                echo "<H3>Bags</H3>";
                foreach($total AS $i => $to){
                    echo "<div class='col-lg-4'>Carton No: <b>$i</b>, Bags Total: <b>$to</b></div>";
                    $bags = $bags + $to;
                }

            }else {

                echo "<br><hr class=\"btn-success\">";
                echo "<H3>Net</H3>";
                foreach($total AS $i => $to){
                    echo "<div class='col-lg-4'>Carton No: <b>$i</b>, Net Total: <b>".($to/1000)." KG, $to G</b></div>";
                    $nets = $nets + $to;
                }
            }
            echo "<br><br>";
            $i++;
        }


        ?>
    </div>
    <hr class="btn-info">
    <div class="row">
        <div class="col-lg-4"><h3>Grand Total</h3></div>
        <div class="col-lg-4"><br>Bags:  <b><?= $bags?></b></div>
        <div class="col-lg-4"><br>Net: <b><?= $nets/1000?> KG, <?= $nets?> G</b></div>
    </div>
    -->
</div>



<style>
    .glyphicon-eye-open{

        display: inline-block;
        padding: 6px 12px;
        margin-bottom: 1px;
        font-size: 14px;
        font-weight: normal;
        line-height: 1.02857143;
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
        background-color: #5bc0de;
        border-color: #2e6da4;

    }
</style>
