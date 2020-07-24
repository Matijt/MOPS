<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use kartik\widgets\ActiveForm;

$this->title = 'Update user: '.$model->username;
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(['id' => 'dynamic-form']); ?>
    <div class="row">
        <div class="col-lg-4">
            <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-lg-4">
            <?= $form->field($updatep, 'password')->passwordInput() ?>
        </div>
        <div class="col-lg-4">
            <?php

            echo  $form->field($model, 'auth_key')->widget(\kartik\select2\Select2::className(), [
                    'data' => ArrayHelper::map(
                        \backend\models\AuthItem::find()->all(),
                        'name', 'name'),
                    'options' =>
                        ['prompt' => 'Select rol',
                        ]
                ]
            )->label('Rol');
            ?>

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
