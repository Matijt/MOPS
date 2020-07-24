<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Crop */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Crop',
]) . $model->crop;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->crop, 'url' => ['view', 'id' => $model->idcrops]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="crop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
