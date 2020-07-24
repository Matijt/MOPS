<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Estimations */

$this->title = Yii::t('app', 'Create Estimations');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Estimations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="estimations-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
