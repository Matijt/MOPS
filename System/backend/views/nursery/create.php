<?php

use yii\helpers\Html;


/* @var $this yii\web\View */
/* @var $model backend\models\Nursery */

$this->title = Yii::t('app', 'Create Nursery');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Nurseries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="nursery-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
