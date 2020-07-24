<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "registrynursery".
 *
 * @property int $id
 * @property int $nursery_idnursery
 * @property string $FM
 * @property string $batch
 * @property string $prodLot
 * @property int $shipment
 * @property string $recDate
 * @property int $seedsRecieved
 * @property int $realSeedsRecieved
 * @property int $seedsUsed
 * @property int $remain
 * @property string $registrynurserycol
 * @property int $order_idorder
 * @property int $numRows
 * @property int $numPlants
 * @property double $estimatedGermination
 * @property double $usedGermination
 * @property int $plantsPerCompartment
 * @property string $sowing
 * @property double $trays
 * @property string $sowedTable
 * @property int $seedsReallyGerminated
 * @property double $germinationReal
 * @property string $transplant
 * @property string $TidelFloor
 * @property int $plantsNurseryNeeded
 * @property int $remainTray
 * @property int $figure_id
 * @property string $remarks
 * @property string $trasplantCompartment
 * @property int $nursery_idnursery1
 *
 * @property Figure $figure
 * @property Nursery $nurseryIdnursery
 * @property Nursery $nurseryIdnursery1
 * @property Order $orderIdorder
 */
class Registrynursery extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'registrynursery';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['nursery_idnursery', 'order_idorder', 'numRows', 'figure_id', 'nursery_idnursery1'], 'required'],
            [['nursery_idnursery', 'shipment', 'seedsRecieved', 'realSeedsRecieved', 'seedsUsed', 'remain', 'order_idorder', 'numRows', 'numPlants', 'plantsPerCompartment', 'seedsReallyGerminated', 'plantsNurseryNeeded', 'remainTray', 'figure_id', 'nursery_idnursery1'], 'integer'],
            [['recDate', 'sowing', 'transplant', 'trasplantCompartment'], 'safe'],
            [['estimatedGermination', 'usedGermination', 'trays', 'germinationReal'], 'number'],
            [['remarks'], 'string'],
            [['FM', 'batch', 'prodLot', 'registrynurserycol'], 'string', 'max' => 45],
            [['sowedTable'], 'string', 'max' => 11],
            [['TidelFloor'], 'string', 'max' => 10],
            [['figure_id'], 'exist', 'skipOnError' => true, 'targetClass' => Figure::className(), 'targetAttribute' => ['figure_id' => 'id']],
            [['nursery_idnursery'], 'exist', 'skipOnError' => true, 'targetClass' => Nursery::className(), 'targetAttribute' => ['nursery_idnursery' => 'idnursery']],
            [['nursery_idnursery1'], 'exist', 'skipOnError' => true, 'targetClass' => Nursery::className(), 'targetAttribute' => ['nursery_idnursery1' => 'idnursery']],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'nursery_idnursery' => Yii::t('app', 'Table Compartment'),
            'FM' => Yii::t('app', 'F/M'),
            'batch' => Yii::t('app', 'Batch'),
            'prodLot' => Yii::t('app', 'Prod Lot'),
            'shipment' => Yii::t('app', 'Shipment'),
            'recDate' => Yii::t('app', 'Rec Date'),
            'seedsRecieved' => Yii::t('app', 'Seeds Recieved'),
            'realSeedsRecieved' => Yii::t('app', 'Real Seeds Recieved'),
            'seedsUsed' => Yii::t('app', 'Seeds Used'),
            'remain' => Yii::t('app', 'Remain'),
            'registrynurserycol' => Yii::t('app', 'Registrynurserycol'),
            'order_idorder' => Yii::t('app', 'Order'),
            'numRows' => Yii::t('app', 'Num Rows'),
            'numPlants' => Yii::t('app', 'Num Plants Per Row'),
            'estimatedGermination' => Yii::t('app', 'Estimated Germination'),
            'usedGermination' => Yii::t('app', 'Used Germination'),
            'plantsPerCompartment' => Yii::t('app', 'Plants Per Compartment'),
            'sowing' => Yii::t('app', 'Sowing'),
            'trays' => Yii::t('app', 'Trays'),
            'sowedTable' => Yii::t('app', 'Sowed Table'),
            'seedsReallyGerminated' => Yii::t('app', 'Germmination PROMEX'),
            'germinationReal' => Yii::t('app', '%'),
            'transplant' => Yii::t('app', 'Transplant'),
            'TidelFloor' => Yii::t('app', 'Tidel Floor'),
            'plantsNurseryNeeded' => Yii::t('app', 'Plants Nursery Needed'),
            'remainTray' => Yii::t('app', 'Remain Tray'),
            'figure_id' => Yii::t('app', 'Figure'),
            'remarks' => Yii::t('app', 'Remarks'),
            'trasplantCompartment' => Yii::t('app', 'Trasplant Compartment'),
            'nursery_idnursery1' => Yii::t('app', 'Flor Compartment'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFigure()
    {
        return $this->hasOne(Figure::className(), ['id' => 'figure_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNurseryIdnursery()
    {
        return $this->hasOne(Nursery::className(), ['idnursery' => 'nursery_idnursery']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNurseryIdnursery1()
    {
        return $this->hasOne(Nursery::className(), ['idnursery' => 'nursery_idnursery1']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(Order::className(), ['idorder' => 'order_idorder']);
    }
}
