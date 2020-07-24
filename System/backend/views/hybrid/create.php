<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Hybrid */

$this->title = Yii::t('app', 'Create Hybrid');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hybrids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hybrid-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
