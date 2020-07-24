<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Hybrid */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Hybrid',
]) . $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hybrids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->variety, 'url' => ['view', 'id' => $model->idHybrid]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="hybrid-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
