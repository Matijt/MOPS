<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Estimations */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Estimations',
]) . $model->orderIdorder->hybridIdHybr->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Estimations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->orderIdorder->hybridIdHybr->variety, 'url' => ['view', 'id' => $model->idestimations]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="estimations-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
