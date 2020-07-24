<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Registry */

$this->title = Yii::t('app', 'Create Registry');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="registry-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'quantities' => $quantities,
    ]) ?>

</div>
