<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Compartment */

$this->title = $model->idCompartment;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Compartments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="compartment-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app', 'Update'), ['update', 'id' => $model->idCompartment], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app', 'Delete'), ['delete', 'id' => $model->idCompartment], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'compNum',
            'rowsNum',
            'grossSurface',
            'netSurface',
            'grossLength',
            'netLength',
            'width',
            'remarks:ntext',
        ],
    ]) ?>

</div>
