<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\Crop */

$this->title = $model->crop;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crop-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'crop',
            'sowingFemale',
            'transplantingMale',
            'transplantingFemale',
            'pollenColectF',
            'pollenColectU',
            'pollinitionF',
            'pollinitionU',
            'harvestF',
            'harvestU',
            'steamDesinfection',
            'durationOfTheCrop',
            'remarks:ntext',
        ],
    ]) ?>

</div>
