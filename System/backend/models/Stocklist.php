<?php

namespace backend\models;

use Yii;
use yii\validators\CompareValidator;

/**
 * This is the model class for table "stocklist".
 *
 * @property int $idstocklist
 * @property int $harvestNumber
 * @property string $harvestDate
 * @property int $numberOfFruitsHarvested
 * @property string $cleaningDate
 * @property int $wetSeedWeight
 * @property int $drySeedWeight
 * @property double $avgWeightOfSeedPF
 * @property int $numberOfBags
 * @property int $cartonNo
 * @property string $shipmentDate
 * @property string $packingListDescription
 * @property string $remarksSeeds
 * @property int $wap
 * @property string $fruitColor
 * @property string $ringColor
 * @property string $destroyed
 * @property double $moisture
 * @property double $tsw
 * @property string $eol
 * @property string $status
 * @property string $hasOrderId
 * @property string $LUser
 *
 * @property StocklistHasOrder[] $stocklistHasOrders
 */
class Stocklist extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stocklist';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['harvestNumber', 'numberOfFruitsHarvested', 'numberOfBags', 'cartonNo', 'hasOrderId', 'wap'], 'integer'],
            [['harvestDate', 'cleaningDate', 'shipmentDate', 'LUser', 'fruitColor', 'ringColor'], 'safe'],
            [['harvestNumber'], 'required'],
            [['avgWeightOfSeedPF', 'moisture', 'tsw', 'wetSeedWeight', 'drySeedWeight'], 'number'],
            [['packingListDescription', 'remarksSeeds'], 'string', 'max' => 255],
            [['destroyed', 'status'], 'string', 'max' => 20],
            [['eol'], 'string', 'max' => 5],
//            ['cleaningDate','compare','compareAttribute'=>'harvestDate','operator'=>'>=','message'=>'Extract date must be after Harvest date.'],
            ['harvestDate', 'required'],
            ['drySeedWeight', 'compare', 'compareAttribute' => 'wetSeedWeight', 'operator' => '<', 'message'=>'Dry Seed Weight must be smaller than Wet Seed Weight.', 'type' => 'number']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idstocklist' => Yii::t('app', 'Idstocklist'),
            'harvestNumber' => Yii::t('app', 'Harvest Number'),
            'harvestDate' => Yii::t('app', 'Harvest Date'),
            'numberOfFruitsHarvested' => Yii::t('app', '# Of Fruits Harvested'),
            'cleaningDate' => Yii::t('app', 'Extract Date'),
            'wetSeedWeight' => Yii::t('app', 'Wet Seed Weight'),
            'drySeedWeight' => Yii::t('app', 'Dry Seed Weight'),
            'avgWeightOfSeedPF' => Yii::t('app', 'Avg Weight Of Seed Pf'),
            'numberOfBags' => Yii::t('app', '# Of Bags'),
            'cartonNo' => Yii::t('app', 'Carton No'),
            'shipmentDate' => Yii::t('app', 'Shipment Date'),
            'packingListDescription' => Yii::t('app', 'Packing List Description'),
            'wap' => Yii::t('app', 'WAP'),
            'fruitColor' => Yii::t('app', 'Fruit Color'),
            'ringColor' => Yii::t('app', 'Ring Color'),
            'remarksSeeds' => Yii::t('app', 'Remarks Seeds'),
            'destroyed' => Yii::t('app', 'Destroyed'),
            'moisture' => Yii::t('app', 'Moisture'),
            'tsw' => Yii::t('app', 'Tsw'),
            'eol' => Yii::t('app', 'Eol'),
            'status' => Yii::t('app', 'Status'),
            'hasOrderId' => Yii::t('app', 'Order Id'),
            'LUser' => Yii::t('app', 'Last User'),

        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocklistHasOrders()
    {
        return $this->hasMany(StocklistHasOrder::className(), ['stocklist_idstocklist' => 'idstocklist']);
    }
    public function getfullDate()
    {
        $genial = date('d-m-Y', strtotime($this->shipmentDate));
        return $genial;
    }
}
