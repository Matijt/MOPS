<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Order */

$this->title = Yii::t('app', 'Create Pollen Collect');
?>
<div class="order-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
