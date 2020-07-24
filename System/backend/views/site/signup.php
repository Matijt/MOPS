

<?php

/* @var $this yii\web\View */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \frontend\models\SignupForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\select2\Select2;

$this->title = 'Register User';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>

    <p>Please fill out the following fields to signup:</p>

    <div class="row">
        <div class="col-lg-6">
            <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>

                <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>
        </div>
        <div class="col-lg-6">
                <?= $form->field($model, 'password')->passwordInput() ?>
        </div>
    </div>
    <div class="row">
        <?php
/*                $form->field($model, 'permissions')->textInput(Select2::widget([
                    'data' => ArrayHelper::map($authItem, 'name', 'name'),
                    'model' => $model,
                    'attribute' => 'permissions',
                    'maintainOrder' => true,
                    'options' => ['placeholder' => 'Select a color ...', 'multiple' => true],
                        ])
                );*/
                ?>

        <?php
        $authItem = ArrayHelper::map($authItem, 'name', 'name');
        //   print_r($authItem);
        ?>
        <?= $form->field($model, 'permissions')->inline(true)->checkboxList($authItem) ?>
                <?php
/*                    $form->field($model, 'permissions')->checkboxList($authItem, array(
                      'options' => array(
                           'value1'=>array('class' => 'col-lg-3'),
                           'value2'=>array('class' => 'col-lg-3'),
                       ),
                    ));
*/                ?>
        </div>
    </div>

    <div class="row">
                <div class="form-group col-lg-4">
                    <?= Html::submitButton('Signup', ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
                </div>
    </div>
            <?php ActiveForm::end(); ?>
    </div>
</div>
