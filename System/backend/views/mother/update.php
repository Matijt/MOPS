<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Mother */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Female',
]) . $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Female'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->variety, 'url' => ['view', 'id' => $model->idMother]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="mother-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
