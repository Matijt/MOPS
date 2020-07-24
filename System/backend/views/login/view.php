<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use kartik\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\Restriccion */

$this->title = $model->username;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Restriccions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="restriccion-view">

    <?php $form = ActiveForm::begin(); ?>


    <?php ActiveForm::end(); ?>

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'username',
            'password_hash',
        ],
    ]) ?>

</div>
