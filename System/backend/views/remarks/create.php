<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Remarks */

$this->title = 'Create Remarks';
$this->params['breadcrumbs'][] = ['label' => 'Remarks', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="remarks-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
