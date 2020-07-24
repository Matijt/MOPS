<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Mother */

$this->title = Yii::t('app', 'Create Female');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Female'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="mother-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
