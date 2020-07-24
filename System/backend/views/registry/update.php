<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model backend\models\Registry */

$this->title = Yii::t('app', 'Update Registry: {name}', [
    'name' => $model->idregistry,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Registries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->idregistry, 'url' => ['view', 'id' => $model->idregistry]];
$this->params['breadcrumbs'][] = Yii::t('app', 'Update');
?>
<div class="registry-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
