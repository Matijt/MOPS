<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Crop */

$this->title = Yii::t('app', 'Create Crop');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Crops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="crop-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
