<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Historialnursery */

$this->title = Yii::t('app', 'Update History of the Nurseries') . $model->title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'History of the Nurseries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->title, 'url' => ['view', 'id' => $model->idhistorialnursery]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="historialnursery-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
