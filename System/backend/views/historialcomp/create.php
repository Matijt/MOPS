<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Historialcomp */

$this->title = Yii::t('app', 'Create Historialcomp');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Historialcomps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="historialcomp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
