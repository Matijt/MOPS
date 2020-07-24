<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "remarks".
 *
 * @property int $id
 * @property string $remark
 *
 * @property Order[] $orders
 */
class Remarks extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'remarks';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['remark'], 'string'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'remark' => Yii::t('app', 'Remark'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['remarks' => 'id']);
    }
}
