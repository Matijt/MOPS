<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use backend\models\Crop;
use backend\models\Mother;
use backend\models\Father;
use kartik\select2\Select2;

/* @var $this yii\web\View */
/* @var $model backend\models\Hybrid */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="hybrid-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'Crop_idcrops')->dropDownList(
        ArrayHelper::map(Crop::find()->andFilterWhere([">", "idcrops",2])->andFilterWhere(['=','delete','0',])->all(), 'idcrops', 'crop'),
        ['prompt' => 'Select the crop']
    ) ?>

    <?= $form->field($model, 'variety')->textInput(['maxlength' => true]) ?>

    <?=
    $form->field($model, 'Father_idFather')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Father::find()->andFilterWhere(['=', 'delete', '0'])->all(), 'idFather', 'variety'),
            'options' =>
                ['prompt' => 'Select the male',]
        ]
    )
    ?>
    <?=
    $form->field($model, 'Mother_idMother')->widget(Select2::classname(), [
            'data' => ArrayHelper::map(Mother::find()->andFilterWhere(['=', 'delete', '0'])->all(), 'idMother', 'variety'),
            'options' =>
                ['prompt' => 'Select the male',]
        ]
    )
    ?>

    <?= $form->field($model, 'remarks')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('app', 'Create') : Yii::t('app', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
