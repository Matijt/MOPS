<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "nursery".
 *
 * @property int $idnursery
 * @property int $numcompartment
 * @property string $tablesFloors
 * @property int $quantity
 *
 * @property Historialnursery[] $historialnurseries
 * @property Order[] $orders
 */
class Nursery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'nursery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numcompartment', 'quantity'], 'integer'],
            [['quantity'], 'required'],
            [['tablesFloors'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idnursery' => Yii::t('app', 'Idnursery'),
            'numcompartment' => Yii::t('app', 'Num Compartment'),
            'tablesFloors' => Yii::t('app', 'Tables / Floors'),
            'quantity' => Yii::t('app', 'Quantity'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistorialnurseries()
    {
        return $this->hasMany(Historialnursery::className(), ['nursery_idnursery' => 'idnursery']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['nursery_idnursery' => 'idnursery']);
    }
}
