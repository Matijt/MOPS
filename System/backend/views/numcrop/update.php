<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Numcrop */

$this->title = Yii::t('app', 'Update {modelClass}: ', [
    'modelClass' => 'Numcrop',
]) . $model->cropnum;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Numcrops'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cropnum, 'url' => ['view', 'id' => $model->cropnum]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="numcrop-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
