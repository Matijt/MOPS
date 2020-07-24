<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use wbraganca\dynamicform\DynamicFormWidget;
use kartik\widgets\ActiveForm;
use kartik\widgets\Select2;

$this->title = 'Create user';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-lg-6">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-lg-6">
            <?= $form->field($model, 'password')->passwordInput() ?>
        </div>

    </div>

    <div class="row">
        <div class="col-lg-12">
                <div class="form-group">
                    <?= Html::submitButton('Guardar', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    </div>
</div>
