<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Stocklist */

$this->title = Yii::t('app', 'Create Stocklist');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Stocklists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="stocklist-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
