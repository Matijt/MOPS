<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "stocklist_has_order".
 *
 * @property int $idstocklist_has_order
 * @property int $stocklist_idstocklist
 * @property int $order_idorder
 * @property int $totalNumberOfFruitsHarvested
 * @property int $totalWetSeedWeight
 * @property int $totalDrySeedWeight
 * @property double $totalAvarageWeightOfSeedsPF
 * @property int $totalNumberOfBags
 * @property int $totalInStock
 * @property int $totalShipped
 * @property double $avarageGP
 * @property double $phase
 * @property double $lotNr
 * @property double $LUser
 *
 * @property Order $orderIdorder
 * @property Stocklist $stocklistIdstocklist
 */
class StocklistHasOrder extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'stocklist_has_order';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['stocklist_idstocklist', 'order_idorder'], 'required'],
            [['stocklist_idstocklist', 'order_idorder', 'totalNumberOfBags', 'lotNr', 'phase'], 'integer'],
            [['LUser'], 'safe'],
            [['totalAvarageWeightOfSeedsPF', 'avarageGP', 'totalNumberOfFruitsHarvested', 'totalWetSeedWeight', 'totalDrySeedWeight', 'totalInStock', 'totalShipped'], 'number'],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
            [['stocklist_idstocklist'], 'exist', 'skipOnError' => true, 'targetClass' => Stocklist::className(), 'targetAttribute' => ['stocklist_idstocklist' => 'idstocklist']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idstocklist_has_order' => Yii::t('app', 'Idstocklist Has Order'),
            'stocklist_idstocklist' => Yii::t('app', 'Harvest number'),
            'order_idorder' => Yii::t('app', 'Lot number'),
            'totalNumberOfFruitsHarvested' => Yii::t('app', 'Total # Of Fruits Harvested'),
            'totalWetSeedWeight' => Yii::t('app', 'Total Wet Seed Weight'),
            'totalDrySeedWeight' => Yii::t('app', 'Total Dry Seed Weight'),
            'totalAvarageWeightOfSeedsPF' => Yii::t('app', 'Total Avarage Weight Of Seeds Pf'),
            'totalNumberOfBags' => Yii::t('app', 'Total # Of Bags'),
            'totalInStock' => Yii::t('app', 'Total In Stock'),
            'totalShipped' => Yii::t('app', 'Total Shipped'),
            'avarageGP' => Yii::t('app', 'Avarage Grams per plant'),
            'phase' => Yii::t('app', 'Phase'),
            'lotNr' => Yii::t('app', 'Lot #'),
            'LUser' => Yii::t('app', 'Last User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(Order::className(), ['idorder' => 'order_idorder']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getStocklistIdstocklist()
    {
        return $this->hasOne(Stocklist::className(), ['idstocklist' => 'stocklist_idstocklist']);
    }

    /**
     * @inheritdoc
     * @return StocklistHasOrderQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new StocklistHasOrderQuery(get_called_class());
    }
}
