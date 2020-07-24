<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Histcrop */

$this->title = Yii::t('app', 'Create Histcrop');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Histcrops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="histcrop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
