<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;

$this->title = 'Perfil';
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Aquí puedes cambiar tu información personal</p>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-lg-4">

            <?= $form->field($updatep, 'email')->textInput(['autofocus' => true, 'type' => 'email']) ?>

        </div>
        <div class="col-lg-4">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($updatep, 'password')->passwordInput() ?>
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
