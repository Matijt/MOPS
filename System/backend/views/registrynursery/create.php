<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Registrynursery */

$this->title = 'Nursery Registration';
$this->params['breadcrumbs'][] = ['label' => 'Registrynurseries', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registrynursery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
