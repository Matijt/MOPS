<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "order".
 *
 * @property integer $numCrop
 * @property double $orderKg
 * @property double $FMRatio
 * @property double $Density
 * @property integer $idorder
 * @property integer $numRows
 * @property integer $numRowsOpt
 * @property integer $netNumOfPlantsF
 * @property integer $netNumOfPlantsM
 * @property string $ReqDeliveryDate
 * @property string $orderDate
 * @property integer $contractNumber
 * @property string $ssRecDate
 * @property integer $sowingM
 * @property integer $sowingF
 * @property integer $nurseryM
 * @property integer $nurseryF
 * @property string $check
 * @property string $sowingDateM
 * @property string $sowingDateF
 * @property integer $realisedNrOfPlantsM
 * @property integer $realisedNrOfPlantsF
 * @property string $transplantingM
 * @property string $transplantingF
 * @property integer $extractedPlantsF
 * @property integer $extractedPlantsM
 * @property integer $remainingPlantsF
 * @property integer $remainingPlantsM
 * @property string $pollenColectF
 * @property string $pollenColectU
 * @property integer $pollenColectQ
 * @property string $pollinationF
 * @property string $pollinationU
 * @property string $harvestF
 * @property string $harvestU
 * @property string $steamDesinfectionF
 * @property string $steamDesinfectionU
 * @property string $remarks
 * @property integer $compartment_idCompartment
 * @property integer $nursery_idnursery
 * @property integer $plantingDistance
 * @property integer $Hybrid_idHybrid
 * @property integer $delete
 * @property string $state
 * @property string $canceledDate
 * @property string $action
 * @property string $prueba
 * @property string $gpOrder
 * @property string $germinationPOF
 * @property string $germinationPOM
 * @property string $prueba2
 * @property string $selector
 * @property string $rfselectorc
 * @property int $trial_id
 * @property int $NumOfPlantsPerRow
 * @property int $NumOfFPRow
 * @property int $NumOfMPRow
 *
 * @property Remarks $remarksIdRemarks
 * @property Hybrid $hybridIdHybr
 * @property Compartment $compartmentIdCompartment
 */
class Order extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numCrop', 'orderKg', 'numRows', 'compartment_idCompartment', 'plantingDistance', 'Hybrid_idHybrid'], 'required'],
            [['numCrop', 'numRows', 'numRowsOpt', 'netNumOfPlantsF', 'netNumOfPlantsM', 'contractNumber', 'sowingM', 'sowingF', 'nurseryM', 'nurseryF', 'realisedNrOfPlantsM', 'realisedNrOfPlantsF', 'extractedPlantsF', 'extractedPlantsM', 'remainingPlantsF', 'remainingPlantsM', 'pollenColectQ', 'plantingDistance', 'Hybrid_idHybrid', 'trial_id', 'NumOfMPRow', 'NumOfFPRow', 'NumOfPlantsPerRow'], 'integer'],
            [['orderKg', 'calculatedYield', 'germinationPOF', 'germinationPOM', 'Density', 'FMRatio'], 'number'],
            [['compartment_idCompartment', 'ReqDeliveryDate', 'orderDate', 'ssRecDate', 'sowingDateM', 'sowingDateF', 'transplantingM', 'transplantingF', 'pollenColectF', 'pollenColectU', 'pollinationF', 'pollinationU', 'harvestF', 'harvestU', 'steamDesinfectionF', 'steamDesinfectionU', 'action', 'prueba', 'prueba2', 'state', 'canceledDate', 'rfselectorc'], 'safe'],
            [['remarks', 'selector'], 'string'],
            [['check'], 'string', 'max' => 45],
            [['NumOfPlantsPerRow'], 'number', 'min' => 2],
            [['NumOfFPRow', 'numRowsOpt', 'gpOrder', 'germinationPOF', 'germinationPOM'], 'number', 'min' => 1],
            [['orderKg'], 'number', 'min' => 0.1],
            [['germinationPOF', 'germinationPOM'], 'number', 'max' => 100],
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
            'numCrop' => Yii::t('app', '# Crop'),
            'orderKg' => Yii::t('app', 'Order Kg'),
            'calculatedYield' => Yii::t('app', 'Calculated yield'),
            'idorder' => Yii::t('app', 'Idorder'),
            'numRows' => Yii::t('app', '# Rows Calc'),
            'numRowsOpt' => Yii::t('app', '# Rows Real'),
            'netNumOfPlantsF' => Yii::t('app', 'Net # Of Plants F'),
            'netNumOfPlantsM' => Yii::t('app', 'Net # Of Plants M'),
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
            'pollenColectF' => Yii::t('app', 'Pollen Harvest Start'),
            'pollenColectU' => Yii::t('app', 'Pollen Harvest End'),
            'pollenColectQ' => Yii::t('app', 'Pollen Harvest Quantity'),
            'pollinationF' => Yii::t('app', 'Pollination Start'),
            'pollinationU' => Yii::t('app', 'Pollination End'),
            'harvestF' => Yii::t('app', 'Harvest Start'),
            'harvestU' => Yii::t('app', 'Harvest End'),
            'steamDesinfectionF' => Yii::t('app', 'Steam Desinfection Start'),
            'steamDesinfectionU' => Yii::t('app', 'Steam Desinfection End'),
            'remarks' => Yii::t('app', 'Remarks'),
            'compartment_idCompartment' => Yii::t('app', 'Compartment'),
            'nursery_idnursery' => Yii::t('app', 'Nursery'),
            'plantingDistance' => Yii::t('app', 'Planting Distance'),
            'Hybrid_idHybrid' => Yii::t('app', 'Hybrid'),
            'state' => Yii::t('app', 'State'),
            'canceledDate' => Yii::t('app', 'Canceled Date'),
            'action' => Yii::t('app', 'You should'),
            'prueba' => Yii::t('app', 'Father'),
            'gpOrder' => Yii::t('app', 'Grams/Plant Order Select'),
            'germinationPOF' => Yii::t('app', 'Germination F'),
            'germinationPOM' => Yii::t('app', 'Germination M'),
            'prueba2' => Yii::t('app', 'Mother'),
            'selector' => Yii::t('app', 'Select'),
            'trial_id' => Yii::t('app', 'Trial'),
            'FMRatio' => Yii::t('app', 'Female/Male Ratio'),
            'Density' => Yii::t('app', 'Density'),
            'NumOfPlantsPerRow' => Yii::t('app', '# Of Plants Per Row'),
            'NumOfFPRow' => Yii::t('app', '# Of Female Plants Per Row'),
            'NumOfMPRow' => Yii::t('app', '# Of Male Plants Per Row'),
            'rfselectorc' => Yii::t('app', 'Reason for change'),
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'order';
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
    public function getRemarksIdRemarks()
    {
        return $this->hasOne(Remarks::className(), ['id' => 'remarksIdRemarks']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompartmentIdCompartment()
    {
        return $this->hasOne(Compartment::className(), ['idCompartment' => 'compartment_idCompartment']);
    }

    public function getfullName()
    {
        $genial = 'Crop: '.$this->numCrop.' Compartment: '.$this->compartmentIdCompartment->compNum.' Hybrid: '.$this->hybridIdHybr->variety." Contract
         number: ".$this->contractNumber;
        return $genial;
    }

    /** Get the label for the history part of the order */

    public function textLabel($column){
        $label = "";
        switch($column){
            case 'c.compNum':
                $label = "'Compartment Number'";
                break;
            case 'h.variety AS Hybrid':
                $label = "'Hybrid'";
                break;
            case 'm.variety As Mother':
                $label = "'Mother'";
                break;
            case 'f.variety AS Father':
                $label = "'Father'";
                break;
            case 'cr.crop':
                $label = "'Crop'";
                break;
            case 'n.numcompartment':
                $label = "'Nursery'";
                break;
            case 'o.numCrop':
                $label = "'Num Crop'";
                break;
            case 'o.orderKg':
                $label = "'Order(KG)'";
                break;
            case 'o.numRows':
                $label = "'Num Rows'";
                break;
        }
        return $label;
    }
}
