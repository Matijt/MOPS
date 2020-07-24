<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "pollen".
 *
 * @property int $idpollen
 * @property int $harvestWeek
 * @property string $harvestDate
 * @property string $harvestMl
 * @property string $useWeek
 * @property string $useMl
 * @property string $youHaveMl
 * @property int $order_idorder
 *
 * @property Order $orderIdorder
 */
class PollenB extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pollen';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['harvestWeek', 'order_idorder'], 'integer'],
            [['harvestDate', 'useWeek'], 'safe'],
            [['harvestMl', 'useMl', 'youHaveMl'], 'number'],
            [['order_idorder'], 'required'],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idpollen' => Yii::t('app', 'Idpollen'),
            'harvestWeek' => Yii::t('app', 'Harvest Week'),
            'harvestDate' => Yii::t('app', 'Harvest Date'),
            'harvestMl' => Yii::t('app', 'Harvest Ml'),
            'useWeek' => Yii::t('app', 'Use Week'),
            'useMl' => Yii::t('app', 'Use Ml'),
            'youHaveMl' => Yii::t('app', 'You Have Ml'),
            'order_idorder' => Yii::t('app', 'Order Idorder'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(Order::className(), ['idorder' => 'order_idorder']);
    }
}
