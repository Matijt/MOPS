<?php

use yii\helpers\Html;
use kartik\export\ExportMenu;
use kartik\grid\GridView;

/* @var $this yii\web\View */
/* @var $model backend\models\Order */
?>

<?= Html::csrfMetaTags() ?>
<div class="order-view">

    <h1>Filter</h1>
    <?php

        echo ExportMenu::widget([
            'dataProvider' => $dataProvider,
            'exportConfig' => [
                ExportMenu::FORMAT_TEXT => false,
                ExportMenu::FORMAT_CSV => false,
                ExportMenu::FORMAT_HTML => false,
                ExportMenu::FORMAT_PDF => false,
            ],
            'filename' => date('d-m-Y'),
        ]);
    echo GridView::widget([
        'dataProvider' => $dataProvider,
    ]);
    ?>

</div>
