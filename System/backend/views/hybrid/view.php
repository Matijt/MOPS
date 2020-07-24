<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use backend\codigo\Facil;

/* @var $this yii\web\View */
/* @var $model backend\models\Hybrid */

$this->title = $model->variety;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app', 'Hybrids'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="hybrid-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            ['attribute' => 'Crop_idCrops',
                'value' => $model->cropIdcrops->crop,
            ],
            'variety',
            ['attribute' => 'Father_idFather',
                'value' => $model->fatherIdFather->variety,
            ],
            ['attribute' => 'Mother_idMother',
                'value' => $model->motherIdMother->variety,
            ],
            [
                'attribute'=>'gpf',
                'value'=> function($model){
                    return Facil::limitarDecimales($model->motherIdMother->gP);
                },
                'label' => "GP From Holland",
            ],
/*            'sowingFemale',
            'transplantingMale',
            'transplantingFemale',
            'pollenColectF',
            'pollenColectU',
            'pollinitionF',
            'pollinitionU',
            'harvestF',
            'harvestU',
            'steamDesinfection',*/
            'remarks:ntext',
        ],
    ]) ?>

</div>
