<?php

namespace backend\codigo;
/**
 * Created by PhpStorm.
 * User: Matias
 * Date: 13/03/2017
 * Time: 08:52 AM
 */

use backend\models\Histcrop;
use Yii;
use backend\models\NumcropHasCompartment;
use backend\models\Numcrop;
use backend\modules\seedsprocess\models\Crop;
use backend\modules\seedsprocess\models\Order;
use backend\modules\seedsprocess\models\Mother;
use backend\modules\seedsprocess\models\Germination;
use backend\modules\seedsprocess\models\OrderSearch;
use backend\models\OrderSearchm;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;

class Facil{

public function crear ($model){


    $model->hybridIdHybr->variety;
    $model->hybridIdHybr->fatherIdFather->variety;
    $model->hybridIdHybr->motherIdMother->variety;



//    $model->germinationPOF = $model->germinationPOF;
  //  $model->germinationPOM = $model->germinationPOM;

    if (!$model->germinationPOM  > 0){
        $model->germinationPOM = $model->hybridIdHybr->motherIdMother->germination;
    }
    if(!$model->germinationPOF > 0){
        $model->germinationPOF  = $model->hybridIdHybr->fatherIdFather->germination;
    }
//     $model->gpOrder = $_POST['gpOrder'];
    $gp = $model->gpOrder;
    $kg = $model->orderKg;

    // 1 = no usar padre, 0 = usar padre en $model->prueba
    if($gp && $kg) {
        // Equivalencia de Kilogramos a gramos.
        $g = $kg*1000;
        // Cantidad de plantas Hembras:
        $cph = floor($g/$gp);
        // Cantidad de plantas Macho:
        if(0 == $model->prueba){
            $cpm = floor($cph/5.25);
        }else{
            $cpm = 0;
        }
        // Cantidad de plantas totales:
        $cpt = $cph+$cpm;
        // Cantidad de líneas:
        $cl = floor(($cpt/75));
        // Inicializamos las variables para la cantidad final de plantas
        $cphT = 0;
        // Evaluamos si se va a usar el macho y asigna un valor a la cantidad de plantas totales:
        if($model->prueba == 1) {
            $cphT = floor($cl*75);
        }else{
            // Evaluamos si la cantidad de líneas es mayor a 4:
            if($cl > 4){
                $cphT = floor($cl*63);
            }else{
                $cphT = floor($cl*60);
            }
        }
        // Sacamos la estimación con respecto a lo que vamos a plantar:
        $estimacionG = (floor($cphT*$gp));
        $estimacionKg = $estimacionG/1000;
    }

    $model->plantingDistance = 50;
    if ($model->germinationPOM  > 0){
        $germinationM = $model->germinationPOM;
    }else{
        $germinationM = $model->hybridIdHybr->motherIdMother->germination;
    }
    if($model->germinationPOF > 0){
        $germinationF = $model->germinationPOF;
    }else{
        $germinationF = $model->hybridIdHybr->fatherIdFather->germination;
    }

    $model->numRows = $cl;
    if ($model->numRowsOpt != null){
        $model->numRows = $model->numRowsOpt;
    }

    if($model->numRows <= 4){
        $ratio = 4;
    }else{
        $ratio = 5.25;
    }
    $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
    $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);
    $model->sowingF = ($model->netNumOfPlantsF/$germinationF)*100;
    $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;

    $model->sowingF = round($model->sowingF);
    $model->sowingM = round($model->sowingM);
    $model->nurseryF = round(($model->netNumOfPlantsF) * 1.15);
    $model->nurseryM = round(($model->netNumOfPlantsM) * 1.15);
    if ($model->hybridIdHybr->motherIdMother->steril == 50) {
        $model->sowingF = ($model->sowingF) * 2;
        $model->nurseryF = ($model->nurseryF) * 2;
    }
    if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
        $model->nurseryM = ($model->nurseryM) * 2;
        $model->sowingM = ($model->sowingM) * 2;
    }
    $model->calculatedYield = ($model->netNumOfPlantsF*$model->gpOrder)/1000;

    $connection = Yii::$app->getDb();
    $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
    $query = $command->queryAll();
    $actualcrop = ArrayHelper::getValue($query, '0');
    $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
    if(!isset($actualcrop)){
        $actualcrop = 1;
    }

    $hola = NumcropHasCompartment::find()->andFilterWhere(['=', 'compartment_idCompartment', $model->compartmentIdCompartment->compNum])
        ->andFilterWhere(['=', 'numcrop_cropnum', $actualcrop])
        ->andFilterWhere(['=' ,'crop_idcrops',1])
        ->all();

    foreach ($hola AS $crop){
        $actualcrop = $actualcrop -1;
    }
// Si el número del crop anterior no es 0, utiliza la fecha del crop actual, si el número de crop anterior es 0 utiliza el actual.

    $lastCrop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' =>  ($actualcrop), 'comp' => $model->compartment_idCompartment])->all();
    $varExtra = 0;
    if(0 == $model->prueba) {
        if ($model->sowingDateM == null || $model->sowingDateM === null) {
            if (($actualcrop - 1) === 0) {
                foreach ($lastCrop AS $item) {
                    $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
                }
                $varExtra = 1;
            }
            if (
                ($crop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' => ($actualcrop) - 1, 'comp' => $model->compartment_idCompartment])->all())
                &&
                ($varExtra == 0)
            ) {
                foreach ($crop AS $item) {
                    $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - 6 day"));
                }
            }
        }
        $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
        $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
        $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
        $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
        $mes = date('n', strtotime($model->sowingDateM));
        $dia = date('j', strtotime($model->sowingDateM));
        $model->compartmentIdCompartment->compNum;
        $F1 = $model->hybridIdHybr->cropIdcrops->sowingFemale;
        $TM = $model->hybridIdHybr->cropIdcrops->transplantingMale;
        $TF = $model->hybridIdHybr->cropIdcrops->transplantingFemale;
        $PF = $model->hybridIdHybr->cropIdcrops->pollinitionF;
        $PU = $model->hybridIdHybr->cropIdcrops->pollinitionU;
        $HF = $model->hybridIdHybr->cropIdcrops->harvestF;
        $HU = $model->hybridIdHybr->cropIdcrops->harvestU;
        $SDA = $model->hybridIdHybr->cropIdcrops->steamDesinfection;

        if ($F1 + $model->hybridIdHybr->sowingFemale >= 0) {
            $model->sowingDateF = date('Y-m-d', strtotime("$model->sowingDateM + " . ($F1 + $model->hybridIdHybr->sowingFemale) . " day"));
        } else {
            $model->sowingDateF = date('Y-m-d', strtotime("$model->sowingDateM " . ($F1 + $model->hybridIdHybr->sowingFemale) . " day"));
        }

        if ($TM + $model->hybridIdHybr->transplantingMale >= 0) {
            $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM + " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
        } else {
            $model->transplantingM = date('Y-m-d', strtotime("$model->sowingDateM " . ($TM + $model->hybridIdHybr->transplantingMale) . " day"));
        }

        if ($TF + $model->hybridIdHybr->transplantingFemale >= 0) {
            $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF + " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
        } else {
            $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
        }

        if (($mes <= 3)) {
            $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
            $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
            if ($mes == 3 && $dia > 10) {
                $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM - 7 day"));
                $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF - 7 day"));
            }
        } elseif (($mes == 12)) {
            if ($dia > 10) {
                $model->transplantingM = date('Y-m-d', strtotime("$model->transplantingM + 7 day"));
                $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
            }
        }

        if (14 + $model->hybridIdHybr->pollenColectF >= 0) {
            $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM + " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
        } else {
            $model->pollenColectF = date('Y-m-d', strtotime("$model->transplantingM " . (14 + $model->hybridIdHybr->pollenColectF) . " day"));
        }

        if (112 + $model->hybridIdHybr->pollenColectU) {
            $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF + " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
        } else {
            $model->pollenColectU = date('Y-m-d', strtotime("$model->pollenColectF " . (112 + $model->hybridIdHybr->pollenColectU) . " day"));
        }

        if ($PF + $model->hybridIdHybr->pollinitionF >= 0) {
            $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF + " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
        } else {
            $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
        }

        if ($PU + $model->hybridIdHybr->pollinitionU >= 0) {
            $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF + " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
        } else {
            $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
        }

        if ($HF + $model->hybridIdHybr->harvestF >= 0) {
            $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF + " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
        } else {
            $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
        }

        if ($HU + $model->hybridIdHybr->harvestU >= 0) {
            $model->harvestU = date('Y-m-d', strtotime("$model->harvestF + " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
        } else {
            $model->harvestU = date('Y-m-d', strtotime("$model->harvestF " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
        }
        $model->steamDesinfectionF = $model->harvestU;

        if ($SDA + $model->hybridIdHybr->steamDesinfection >= 0) {
            $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
        } else {
            $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
        }

        if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
            $model->check = "Great, no problem.";
        } else {
            $model->check = "Check!";
        }
    }else{
        if ($model->sowingDateF == null || $model->sowingDateF === null) {
            if (($actualcrop - 1) === 0) {
                foreach ($lastCrop AS $item) {
                    $model->sowingDateF = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
                }
                $varExtra = 1;
            }
            if (
                ($crop = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' => ($actualcrop) - 1, 'comp' => $model->compartment_idCompartment])->all())
                &&
                ($varExtra == 0)
            ) {
                foreach ($crop AS $item) {
                    $model->sowingDateF = date('Y-m-d', strtotime("$item->freeDate + 8 day"));
                }
            }
        }

        $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
        $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
        $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
        $model->sowingDateF = date('Y-m-d', strtotime($model->sowingDateF));
        $mes = date('n', strtotime($model->sowingDateF));
        $dia = date('j', strtotime($model->sowingDateF));
        $model->compartmentIdCompartment->compNum;
        $TF = $model->hybridIdHybr->cropIdcrops->transplantingFemale;
        $HF = $model->hybridIdHybr->cropIdcrops->harvestF;
        $HU = $model->hybridIdHybr->cropIdcrops->harvestU;
        $PF = $model->hybridIdHybr->cropIdcrops->pollinitionF;
        $PU = $model->hybridIdHybr->cropIdcrops->pollinitionU;
        $SDA = $model->hybridIdHybr->cropIdcrops->steamDesinfection;

        if ($TF + $model->hybridIdHybr->transplantingFemale >= 0) {
            $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF + " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
        } else {
            $model->transplantingF = date('Y-m-d', strtotime("$model->sowingDateF " . ($TF + $model->hybridIdHybr->transplantingFemale) . " day"));
        }

        if (($mes <= 3)) {
            $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
            if ($mes == 3 && $dia > 10) {
                $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF - 7 day"));
            }
        } elseif (($mes == 12)) {
            if ($dia > 10) {
                $model->transplantingF = date('Y-m-d', strtotime("$model->transplantingF + 7 day"));
            }
        }

        if ($PF + $model->hybridIdHybr->pollinitionF >= 0) {
            $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF + " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
        } else {
            $model->pollinationF = date('Y-m-d', strtotime("$model->transplantingF " . ($PF + $model->hybridIdHybr->pollinitionF) . " day"));
        }

        if ($PU + $model->hybridIdHybr->pollinitionU >= 0) {
            $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF + " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
        } else {
            $model->pollinationU = date('Y-m-d', strtotime("$model->pollinationF " . ($PU + $model->hybridIdHybr->pollinitionU) . " day"));
        }

        if ($HF + $model->hybridIdHybr->harvestF >= 0) {
            $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF + " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
        } else {
            $model->harvestF = date('Y-m-d', strtotime("$model->pollinationF " . ($HF + $model->hybridIdHybr->harvestF) . " day"));
        }

        if ($HU + $model->hybridIdHybr->harvestU >= 0) {
            $model->harvestU = date('Y-m-d', strtotime("$model->harvestF + " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
        } else {
            $model->harvestU = date('Y-m-d', strtotime("$model->harvestF " . ($HU + $model->hybridIdHybr->harvestU) . " day"));
        }
        $model->steamDesinfectionF = $model->harvestU;

        if ($SDA + $model->hybridIdHybr->steamDesinfection >= 0) {
            $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF + " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
        } else {
            $model->steamDesinfectionU = date('Y-m-d', strtotime("$model->steamDesinfectionF " . ($SDA + $model->hybridIdHybr->steamDesinfection) . " day"));
        }

        if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
            $model->check = "Great, no problem.";
        } else {
            $model->check = "Check!";
        }
    }
    $connection = Yii::$app->getDb();
    $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
    $query = $command->queryAll();
    $actualcrop = ArrayHelper::getValue($query, '0');
    $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
    if(!isset($actualcrop)){
        $actualcrop = 1;
    }
    $model->numCrop = $actualcrop;

    $rowsAll = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, rowsOccupied, rowsLeft
    FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment AND numcrop_cropnum = :numcomp", [':compartment' => $model->compartment_idCompartment, ':numcomp' => $actualcrop]);
    $queryR = $rowsAll->queryAll();
    $actualrows = ArrayHelper::getValue($queryR, '0');

    $rowsO = ArrayHelper::getValue($actualrows, 'rowsOccupied');
    $rowsL = ArrayHelper::getValue($actualrows, 'rowsLeft');

    $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS maxCrop
            FROM numcrop_has_compartment");
    $maxCrop = $command->queryAll();
    $maxCrop = ArrayHelper::getValue($maxCrop, '0');
    $maxCrop = ArrayHelper::getValue($maxCrop, 'maxCrop');
}

    public function editar($model, $models){
        // número de crop
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, compartment_idCompartment AS comp,
                                                   freeDate, rowsOccupied, rowsLeft FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
        $query = $command->queryAll();
        $actualcrop = ArrayHelper::getValue($query, '0');
        $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
        // fechas


        $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
        $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
        $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
        $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
        $model->sowingDateF = date('Y-m-d', strtotime($model->sowingDateF));
        $model->transplantingM = date('Y-m-d', strtotime($model->transplantingM));
        $model->transplantingF = date('Y-m-d', strtotime($model->transplantingF));
        $model->pollenColectF = date('Y-m-d', strtotime($model->pollenColectF));
        $model->pollenColectU = date('Y-m-d', strtotime($model->pollenColectU));
        $model->pollinationF = date('Y-m-d', strtotime($model->pollinationF));
        $model->pollinationU = date('Y-m-d', strtotime($model->pollinationU));
        $model->harvestF = date('Y-m-d', strtotime($model->harvestF));
        $model->harvestU = date('Y-m-d', strtotime($model->harvestU));
        $model->steamDesinfectionF = date('Y-m-d', strtotime($model->steamDesinfectionF));
        $model->steamDesinfectionU = date('Y-m-d', strtotime($model->steamDesinfectionU));


        if(!($model->numRows == $models->numRows) && $model->germinationPOF > 0 && $model->germinationPOM > 0){
            $germinationM = $model->germinationPOM;
            $germinationF = $model->germinationPOF;

            if($model->numRows <= 4){
                $ratio = 4;
            }else{
                $ratio = 5.25;
            }

            $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);

            if ($germinationF > 0){
                $model->sowingF = ($model->netNumOfPlantsF/$germinationF)*100;
            }else{
                $model->sowingF = 0;
            }
            if ($germinationM > 0){
                $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;
            }else{
                $model->sowingM = 0;
            }

            $model->sowingF = round($model->sowingF);
            $model->sowingM = round($model->sowingM);
            $model->nurseryF = round(($model->netNumOfPlantsF) * 1.15);
            $model->nurseryM = round(($model->netNumOfPlantsM) * 1.15);
            if ($model->hybridIdHybr->motherIdMother->steril == 50) {
                $model->sowingF = ($model->sowingF) * 2;
                $model->nurseryF = ($model->nurseryF) * 2;
            }
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }
            $model->calculatedYield = ($model->netNumOfPlantsF*$model->hybridIdHybr->motherIdMother->gP)/1000;
        }

        $cropUse = $model->numCrop;

        if(!($model->numCrop == $models->numCrop)){

            $cropEdit = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' =>  ($model->numCrop-1), 'comp' => $model->compartmentIdCompartment->idCompartment])->all();

            foreach ($cropEdit as$item) {
                $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
            }
            $model->hybridIdHybr->save();
            $item = $model;

//          "Evaluación de la fecha para ver si es en invierno.";
            $mes = date('n', strtotime($item->sowingDateM));
            $dia = date('j', strtotime($item->sowingDateM));

            if (($mes <= 3)) {
                $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM + 7 day"));
                $item->transplantingF = date('Y-m-d', strtotime("$item->transplantingF + 7 day"));
                if ($mes == 3 && $dia > 10) {
                    $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM - 7 day"));
                    $item->transplantingF = date('Y-m-d', strtotime("$item->transplantingF - 7 day"));
                }
            } elseif (($mes == 12)) {
                if ($dia > 10) {
                    $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM + 7 day"));
                    $item->transplantingF = date('Y-m-d', strtotime("$item->transplantingF + 7 day"));
                }
            }
        }
        // Cambiar los valores
        if($model->realisedNrOfPlantsM && $model->extractedPlantsM){
            $model->remainingPlantsM = $model->realisedNrOfPlantsM-$model->extractedPlantsM;
        }else{
            $model->remainingPlantsM = null;
        }

        if($model->realisedNrOfPlantsF && $model->extractedPlantsF){
            $model->remainingPlantsF = $model->realisedNrOfPlantsF-$model->extractedPlantsF;
        }else{
            $model->remainingPlantsF = null;
        }
//            $model->hybridIdHybr->cropIdcrops->save()

        if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
            $model->check = "Great, no problem.";
        } else {
            $model->check = "Check it";
        }
    }



    public function editarpc($model, $models){
        // número de crop
        $connection = Yii::$app->getDb();
        $command = $connection->createCommand("SELECT MAX(numcrop_cropnum) AS actualCrop, compartment_idCompartment AS comp,
                                                   freeDate, rowsOccupied, rowsLeft FROM numcrop_has_compartment WHERE compartment_idCompartment = :compartment", [':compartment' => $model->compartment_idCompartment]);
        $query = $command->queryAll();
        $actualcrop = ArrayHelper::getValue($query, '0');
        $actualcrop = ArrayHelper::getValue($actualcrop, 'actualCrop');
        // fechas


        $model->ReqDeliveryDate = date('Y-m-d', strtotime($model->ReqDeliveryDate));
        $model->orderDate = date('Y-m-d', strtotime($model->orderDate));
        $model->ssRecDate = date('Y-m-d', strtotime($model->ssRecDate));
        $model->sowingDateM = date('Y-m-d', strtotime($model->sowingDateM));
        $model->transplantingM = date('Y-m-d', strtotime($model->transplantingM));
        $model->pollenColectF = date('Y-m-d', strtotime($model->pollenColectF));
        $model->pollenColectU = date('Y-m-d', strtotime($model->pollenColectU));
        $model->steamDesinfectionF = date('Y-m-d', strtotime($model->steamDesinfectionF));
        $model->steamDesinfectionU = date('Y-m-d', strtotime($model->steamDesinfectionU));


        if(!($model->numRows == $models->numRows) && $model->germinationPOF > 0 && $model->germinationPOM > 0){
            $germinationM = $model->germinationPOM;

            if($model->numRows <= 4){
                $ratio = 4;
            }else{
                $ratio = 5.25;
            }

            $model->netNumOfPlantsF = round((((3775/$model->plantingDistance)*$ratio)/(1+$ratio))*$model->numRows);
            $model->netNumOfPlantsM = round((((3775/$model->plantingDistance))/(1+$ratio))*$model->numRows);

            if ($germinationM > 0){
                $model->sowingM = ($model->netNumOfPlantsM/$germinationM)*100;
            }else{
                $model->sowingM = 0;
            }

            $model->sowingF = round($model->sowingF);
            $model->sowingM = round($model->sowingM);
            $model->nurseryF = round(($model->netNumOfPlantsF) * 1.15);
            $model->nurseryM = round(($model->netNumOfPlantsM) * 1.15);
            if ($model->hybridIdHybr->motherIdMother->steril == 50) {
                $model->sowingF = ($model->sowingF) * 2;
                $model->nurseryF = ($model->nurseryF) * 2;
            }
            if ($model->hybridIdHybr->fatherIdFather->steril == 50) {
                $model->nurseryM = ($model->nurseryM) * 2;
                $model->sowingM = ($model->sowingM) * 2;
            }
            $model->calculatedYield = ($model->netNumOfPlantsF*$model->hybridIdHybr->motherIdMother->gP)/1000;
        }

        $cropUse = $model->numCrop;

        if(!($model->numCrop == $models->numCrop)){

            $cropEdit = NumcropHasCompartment::find()->where('(numcrop_cropnum = :crop) AND compartment_idCompartment = :comp', ['crop' =>  ($model->numCrop-1), 'comp' => $model->compartmentIdCompartment->idCompartment])->all();

            foreach ($cropEdit as$item) {
                $model->sowingDateM = date('Y-m-d', strtotime("$item->freeDate - " . (($model->hybridIdHybr->cropIdcrops->transplantingMale) - 1) . " day"));
            }
            $model->hybridIdHybr->save();
            $item = $model;

//          "Evaluación de la fecha para ver si es en invierno.";
            $mes = date('n', strtotime($item->sowingDateM));
            $dia = date('j', strtotime($item->sowingDateM));

            if (($mes <= 3)) {
                $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM + 7 day"));
                if ($mes == 3 && $dia > 10) {
                    $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM - 7 day"));
                }
            } elseif (($mes == 12)) {
                if ($dia > 10) {
                    $item->transplantingM = date('Y-m-d', strtotime("$item->transplantingM + 7 day"));
                }
            }
        }
        if($model->realisedNrOfPlantsM && $model->extractedPlantsM){
            $model->remainingPlantsM = $model->realisedNrOfPlantsM-$model->extractedPlantsM;
        }else{
            $model->remainingPlantsM = null;
        }

        if (!($model->steamDesinfectionU >= $model->ReqDeliveryDate)) {
            $model->check = "Great, no problem.";
        } else {
            $model->check = "Check it";
        }
    }

    public function limitarDecimales($numero){
        if (is_float($numero)){
            return round($numero, 2);
        }else{
            return $numero;
        }
    }
}


?>

