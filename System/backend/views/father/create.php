<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Father */

$this->title = Yii::t('app', 'Create Male');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Males'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="father-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
