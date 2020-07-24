<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Historialnursery */

$this->title = Yii::t('app', 'Create History of the Nurseries');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Historialnurseries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historialnursery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
