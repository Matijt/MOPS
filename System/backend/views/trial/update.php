<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Trial */

$this->title = Yii::t('app', 'Update Trial: ' . $model->reason.', Compartment: '.$model->compartmentIdCompartment->compNum, [
    'nameAttribute' => '' . $model->id_trial,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Trials'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id_trial, 'url' => ['view', 'id' => $model->id_trial]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="trial-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'modelsorder' => (empty($modelsorder)) ? \backend\models\Order::find()->andFilterWhere(['=', 'trial_id', $model->id_trial])->all() : $modelsorder
    ]) ?>

</div>
