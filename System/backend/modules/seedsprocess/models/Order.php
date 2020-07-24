<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property int $numCrop
 * @property double $orderKg
 * @property double $calculatedYield
 * @property int $idorder
 * @property int $numRows
 * @property int $netNumOfPlantsF
 * @property int $netNumOfPlantsM
 * @property string $ReqDeliveryDate
 * @property string $orderDate
 * @property int $contractNumber
 * @property string $ssRecDate
 * @property int $sowingM
 * @property int $sowingF
 * @property int $nurseryM
 * @property int $nurseryF
 * @property string $check
 * @property string $sowingDateM
 * @property string $sowingDateF
 * @property int $realisedNrOfPlantsM
 * @property int $realisedNrOfPlantsF
 * @property string $transplantingM
 * @property string $transplantingF
 * @property int $extractedPlantsF
 * @property int $extractedPlantsM
 * @property int $remainingPlantsF
 * @property int $remainingPlantsM
 * @property string $pollenColectF
 * @property string $pollenColectU
 * @property int $pollenColectQ
 * @property string $pollinationF
 * @property string $pollinationU
 * @property string $harvestF
 * @property string $harvestU
 * @property string $steamDesinfectionF
 * @property string $steamDesinfectionU
 * @property string $remarks
 * @property int $compartment_idCompartment
 * @property int $plantingDistance
 * @property int $Hybrid_idHybrid
 * @property string $state
 * @property string $action
 * @property string $prueba
 * @property string $prueba2
 * @property string $selector
 * @property string $gpOrder
 *
 * @property Hybrid $hybridIdHybr
 * @property Compartment $compartmentIdCompartment
 * @property OrderHasNumcropHasCompartment[] $orderHasNumcropHasCompartments
 * @property NumcropHasCompartment[] $numcropHasCompartmentNumcropCropnums
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numCrop', 'orderKg', 'calculatedYield', 'numRows', 'pollenColectF', 'pollenColectU', 'compartment_idCompartment', 'plantingDistance', 'Hybrid_idHybrid', 'state'], 'required'],
            [['numCrop', 'numRows', 'netNumOfPlantsF', 'netNumOfPlantsM', 'contractNumber', 'sowingM', 'sowingF', 'nurseryM', 'nurseryF', 'realisedNrOfPlantsM', 'realisedNrOfPlantsF', 'extractedPlantsF', 'extractedPlantsM', 'remainingPlantsF', 'remainingPlantsM', 'pollenColectQ', 'compartment_idCompartment', 'plantingDistance', 'Hybrid_idHybrid'], 'integer'],
            [['orderKg', 'calculatedYield'], 'number'],
            [['ReqDeliveryDate', 'orderDate', 'ssRecDate', 'sowingDateM', 'sowingDateF', 'transplantingM', 'transplantingF', 'pollenColectF', 'pollenColectU', 'pollinationF', 'pollinationU', 'harvestF', 'harvestU', 'steamDesinfectionF', 'steamDesinfectionU'], 'safe'],
            [['remarks', 'gpOrder', 'action', 'prueba','prueba2', 'selector'], 'string'],
            [['check'], 'string', 'max' => 45],
            [['state'], 'string', 'max' => 255],
            [['Hybrid_idHybrid'], 'exist', 'skipOnError' => true, 'targetClass' => Hybrid::className(), 'targetAttribute' => ['Hybrid_idHybrid' => 'idHybrid']],
            [['compartment_idCompartment'], 'exist', 'skipOnError' => true, 'targetClass' => Compartment::className(), 'targetAttribute' => ['compartment_idCompartment' => 'idCompartment']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'numCrop' => Yii::t('app', 'Num Crop'),
            'orderKg' => Yii::t('app', 'Order Kg'),
            'calculatedYield' => Yii::t('app', 'Calculated Yield'),
            'idorder' => Yii::t('app', 'Idorder'),
            'numRows' => Yii::t('app', 'Num Rows'),
            'netNumOfPlantsF' => Yii::t('app', 'Net Num Of Plants F'),
            'netNumOfPlantsM' => Yii::t('app', 'Net Num Of Plants M'),
            'ReqDeliveryDate' => Yii::t('app', 'Req Delivery Date'),
            'orderDate' => Yii::t('app', 'Order Date'),
            'contractNumber' => Yii::t('app', 'Contract Number'),
            'ssRecDate' => Yii::t('app', 'Ss Rec Date'),
            'sowingM' => Yii::t('app', 'Sowing M'),
            'sowingF' => Yii::t('app', 'Sowing F'),
            'nurseryM' => Yii::t('app', 'Nursery M'),
            'nurseryF' => Yii::t('app', 'Nursery F'),
            'check' => Yii::t('app', 'Check'),
            'sowingDateM' => Yii::t('app', 'Sowing Date M'),
            'sowingDateF' => Yii::t('app', 'Sowing Date F'),
            'realisedNrOfPlantsM' => Yii::t('app', 'Realised Nr Of Plants M'),
            'realisedNrOfPlantsF' => Yii::t('app', 'Realised Nr Of Plants F'),
            'transplantingM' => Yii::t('app', 'Transplanting M'),
            'transplantingF' => Yii::t('app', 'Transplanting F'),
            'extractedPlantsF' => Yii::t('app', 'Extracted Plants F'),
            'extractedPlantsM' => Yii::t('app', 'Extracted Plants M'),
            'remainingPlantsF' => Yii::t('app', 'Remaining Plants F'),
            'remainingPlantsM' => Yii::t('app', 'Remaining Plants M'),
            'pollenColectF' => Yii::t('app', 'Pollen Colect F'),
            'pollenColectU' => Yii::t('app', 'Pollen Colect U'),
            'pollenColectQ' => Yii::t('app', 'Pollen Colect Q'),
            'pollinationF' => Yii::t('app', 'Pollination F'),
            'pollinationU' => Yii::t('app', 'Pollination U'),
            'harvestF' => Yii::t('app', 'Harvest F'),
            'harvestU' => Yii::t('app', 'Harvest U'),
            'steamDesinfectionF' => Yii::t('app', 'Steam Desinfection F'),
            'steamDesinfectionU' => Yii::t('app', 'Steam Desinfection U'),
            'remarks' => Yii::t('app', 'Remarks'),
            'compartment_idCompartment' => Yii::t('app', 'Compartment'),
            'plantingDistance' => Yii::t('app', 'Planting Distance'),
            'Hybrid_idHybrid' => Yii::t('app', 'Hybrid'),
            'state' => Yii::t('app', 'State'),
            'action' => Yii::t('app', 'Action'),
            'prueba' => Yii::t('app', 'Father'),
            'prueba2' => Yii::t('app', 'Mother'),
            'selector' => Yii::t('app', 'Select'),
            'gpOrder' => Yii::t('app', 'Gp Order'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHybridIdHybr()
    {
        return $this->hasOne(Hybrid::className(), ['idHybrid' => 'Hybrid_idHybrid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompartmentIdCompartment()
    {
        return $this->hasOne(Compartment::className(), ['idCompartment' => 'compartment_idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderHasNumcropHasCompartments()
    {
        return $this->hasMany(OrderHasNumcropHasCompartment::className(), ['order_idorder' => 'idorder']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumcropHasCompartmentNumcropCropnums()
    {
        return $this->hasMany(NumcropHasCompartment::className(), ['numcrop_cropnum' => 'numcrop_has_compartment_numcrop_cropnum', 'compartment_idCompartment' => 'numcrop_has_compartment_compartment_idCompartment'])->viaTable('order_has_numcrop_has_compartment', ['order_idorder' => 'idorder']);
    }
}
