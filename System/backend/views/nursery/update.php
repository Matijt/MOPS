<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Nursery */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Nursery',
]) . $model->idnursery;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Nurseries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idnursery, 'url' => ['view', 'id' => $model->idnursery]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="nursery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
