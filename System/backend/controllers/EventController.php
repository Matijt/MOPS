<?php

namespace backend\controllers;

use backend\models\Color;
use backend\models\Order;
use Yii;
use backend\models\Event;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use yii2mod\rbac\filters\AccessControl;

/**
 * EventController implements the CRUD actions for Event model.
 */
class EventController extends Controller
{
    /**
     * @inheritdoc
     */
    public function behaviors()
    {
        return [
            'access' => [
                'class' => AccessControl::className(),
                'rules' => [
                    [
                        'actions' => ['index', 'calendarg', 'calendargc'],
                        'allow' => true,
                        'roles' => ['@'],
                    ],
                ],
            ],
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    /**
     * Lists all Event models.
     * @return mixed
     */
    public function actionIndex()
    {

//        ini_set('memory_limit','4096M');

        $orderDbs = Order::find()->andFilterWhere(['=', 'order.delete',0])
        ->andFilterWhere(['=', 'order.state', 'Active'])
        ->all();
        foreach ($orderDbs AS $orderDb) {
            if($orderDb->ssRecDate) {
                // Recivir semilla
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->ssRecDate;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 1])->color;
                $eventsfull2[] = $eventfull;
            }

            // Se planta el macho
            $eventfull = new \edofre\fullcalendarscheduler\models\Event();
            $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

            // Se planta el macho
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->sowingDateM) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->sowingDateM;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 2])->color;
                $eventsfull2[] = $eventfull;
            }

            // Se planta la hembra
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->sowingDateF) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->sowingDateF;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 3])->color;
                $eventsfull2[] = $eventfull;
            }

            // Se transplanta el macho
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->transplantingM) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->transplantingM;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 4])->color;
                $eventsfull2[] = $eventfull;
            }

            // Se transplanta a la hembra
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->transplantingF) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->transplantingF;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 5])->color;
                $eventsfull2[] = $eventfull;
            }

            // Colecta de polen
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->pollenColectF && $orderDb->pollenColectU) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);
                $eventfull->className = "hovers";

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->pollenColectF;
                $eventfull->end = $orderDb->pollenColectU;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 6])->color;
                $eventsfull2[] = $eventfull;
            }

            // Polinización
            if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->pollinationF && $orderDb->pollinationU) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);
                $eventfull->className = 'Pollination';

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->pollinationF;
                $eventfull->end = $orderDb->pollinationU;
                $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 7])->color;
                $eventsfull2[] = $eventfull;
            }

        // Cosecha
        if($orderDb->harvestF && $orderDb->harvestF) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);
            $eventfull->className = "hovers";

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->harvestF;
                $eventfull->end = $orderDb->harvestU;
            $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 8])->color;
                $eventsfull2[] = $eventfull;
            }

        // Limpieza
        if ($orderDb->steamDesinfectionF && $orderDb->steamDesinfectionU) {
                $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                $eventfull->id = $orderDb->idorder;
                $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                $eventfull->start = $orderDb->steamDesinfectionF;
                $eventfull->end = $orderDb->steamDesinfectionU;
            $eventfull->title = $orderDb->hybridIdHybr->variety;
                $eventfull->textColor = "black";
                $eventfull->backgroundColor = Color::findOne(['idcolor' => 9])->color;
                $eventsfull2[] = $eventfull;
            }
        }


        return $this->render('index', [
            'eventsfull' => $eventsfull2,
        ]);
    }

    public function actionCalendargc ($comps, $acts) {

        ini_set('memory_limit','4092M');
        $count =0;
        $count2 =0;
        $filter ="";
        $filter2 ="";
        $comps = explode(',', $comps);
        $acts = explode(',', $acts);
        foreach ($comps AS $comp){
            if($count > 0) {
                $filter = $filter . " OR c.`compNum` = " . $comp;
            }else{
                $filter = $filter."c.`compNum` = " . $comp;
                $count++;
            }
        }
        foreach ($acts AS $act){
            if($count2> 0) {
                $filter2 = $filter2 . " OR `color_idcolor` = " . $act;
            }else{
                $filter2 = $filter2."`color_idcolor` = " . $act;
                $count2++;
            }
        }
        $sql = 'SELECT * FROM `order` o INNER JOIN `compartment` c WHERE  ('.$filter.') AND `delete` = 0 AND o.`steamDesinfectionU` > "'.date('Y-m-d').'"';
        $orderDBFilter = Order::findBySql($sql)->all();
        if ($orderDBFilter == null){
            echo "NO DATA";
            die;
        }else {
            foreach ($orderDBFilter AS $orderDb) {
                if ($orderDb->ssRecDate && in_array(1, $acts)) {
                    // Recivir semilla
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->ssRecDate;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 1])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se planta el macho
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->sowingDateM && in_array(2, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->sowingDateM;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 2])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se planta la hembra
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->sowingDateF && in_array(3, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->sowingDateF;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 3])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se transplanta el macho
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->transplantingM && in_array(4, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->transplantingM;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 4])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se transplanta a la hembra
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->transplantingF && in_array(5, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->transplantingF;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 5])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Colecta de polen
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->pollenColectF && $orderDb->pollenColectU && in_array(6, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->pollenColectF;
                    $eventfull->end = $orderDb->pollenColectU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 6])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Polinización
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->pollinationF && $orderDb->pollinationU && in_array(7, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->pollinationF;
                    $eventfull->end = $orderDb->pollinationU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 7])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Cosecha
                if ($orderDb->harvestF && $orderDb->harvestF && in_array(8, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->harvestF;
                    $eventfull->end = $orderDb->harvestU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 8])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Limpieza
                if ($orderDb->steamDesinfectionF && $orderDb->steamDesinfectionU && in_array(9, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->steamDesinfectionF;
                    $eventfull->end = $orderDb->steamDesinfectionU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 9])->color;
                    $eventsfull2[] = $eventfull;
                }
            }
            $count = 0;
            foreach ($comps AS $comp) {
                $compartment = new \edofre\fullcalendarscheduler\models\Resource();
                $compartment->id = $comp;
                $compartment->title = $comp;
                $compartments[] = $compartment;
            }
        }

        return $this->renderAjax('calendarg', [
            'eventsfull' => $eventsfull2,
            'compartments' => $compartments
        ]);
    }

    public function actionCalendarg ($comps, $acts) {

        ini_set('memory_limit','4092M');
        $count =0;
        $count2 =0;
        $filter ="";
        $filter2 ="";
        $comps = explode(',', $comps);
        $acts = explode(',', $acts);
        foreach ($comps AS $comp){
            if($count > 0) {
                $filter = $filter . " OR c.`compNum` = " . $comp;
            }else{
                $filter = $filter."c.`compNum` = " . $comp;
                $count++;
            }
        }
        foreach ($acts AS $act){
            if($count2> 0) {
                $filter2 = $filter2 . " OR `color_idcolor` = " . $act;
            }else{
                $filter2 = $filter2."`color_idcolor` = " . $act;
                $count2++;
            }
        }
        $sql = 'SELECT * FROM `order` o INNER JOIN `compartment` c WHERE  ('.$filter.') AND `delete` = 0 AND o.`steamDesinfectionU` > '.date('Y-m-d');

        $orderDBFilter = Order::findBySql($sql)->all();
        if ($orderDBFilter == null){
            echo "NO DATA";
            die;
        }else {
            foreach ($orderDBFilter AS $orderDb) {
                if ($orderDb->ssRecDate && in_array(1, $acts)) {
                    // Recivir semilla
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->ssRecDate;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 1])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se planta el macho
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->sowingDateM && in_array(2, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->sowingDateM;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 2])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se planta la hembra
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->sowingDateF && in_array(3, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->sowingDateF;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 3])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se transplanta el macho
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->transplantingM && in_array(4, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->transplantingM;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 4])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Se transplanta a la hembra
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->transplantingF && in_array(5, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->transplantingF;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 5])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Colecta de polen
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->motherIdMother->variety && $orderDb->pollenColectF && $orderDb->pollenColectU && in_array(6, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->pollenColectF;
                    $eventfull->end = $orderDb->pollenColectU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 6])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Polinización
                if ($orderDb->hybridIdHybr->variety != $orderDb->hybridIdHybr->fatherIdFather->variety && $orderDb->pollinationF && $orderDb->pollinationU && in_array(7, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->pollinationF;
                    $eventfull->end = $orderDb->pollinationU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 7])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Cosecha
                if ($orderDb->harvestF && $orderDb->harvestF && in_array(8, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->harvestF;
                    $eventfull->end = $orderDb->harvestU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 8])->color;
                    $eventsfull2[] = $eventfull;
                }

                // Limpieza
                if ($orderDb->steamDesinfectionF && $orderDb->steamDesinfectionU && in_array(9, $acts)) {
                    $eventfull = new \edofre\fullcalendarscheduler\models\Event();
                    $eventfull->setAttributes(['schedulerLicenseKey' => '0185618251-fcs-1508340158']);

                    $eventfull->id = $orderDb->idorder;
                    $eventfull->resourceId = $orderDb->compartmentIdCompartment->compNum;
                    $eventfull->start = $orderDb->steamDesinfectionF;
                    $eventfull->end = $orderDb->steamDesinfectionU;
                    $eventfull->title = $orderDb->hybridIdHybr->variety;
                    $eventfull->textColor = "black";
                    $eventfull->backgroundColor = Color::findOne(['idcolor' => 9])->color;
                    $eventsfull2[] = $eventfull;
                }
            }
            $count = 0;
            foreach ($comps AS $comp) {
                $compartment = new \edofre\fullcalendarscheduler\models\Resource();
                $compartment->id = $comp;
                $compartment->title = $comp;
                $compartments[] = $compartment;
            }
        }

        return $this->renderAjax('calendarg', [
            'eventsfull' => $eventsfull2,
            'compartments' => $compartments
        ]);
    }
}
