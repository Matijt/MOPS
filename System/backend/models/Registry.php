<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "registry".
 *
 * @property int $idregistry
 * @property int $quantity
 * @property int $quantity2
 * @property int $numRow
 * @property int $fruitsCount
 * @property int $order_idorder
 * @property int $LUser
 *
 * @property Order $orderIdorder
 */
class Registry extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'registry';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['fruitsCount'], 'safe'],
            [['quantity', 'quantity2' , 'order_idorder', 'numRow'], 'integer'],
            [['quantity','quantity2', 'numRow'], 'number'],
            [['order_idorder'], 'required'],
            [['LUser'], 'safe'],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idregistry' => Yii::t('app', 'Idregistry'),
            'quantity' => Yii::t('app', 'Fruits Quantity 1'),
            'quantity2' => Yii::t('app', 'Fruits Quantity 2'),
            'numRow' => Yii::t('app', '# Row'),
            'fruitsCount' => Yii::t('app', 'Plants Count'),
            'order_idorder' => Yii::t('app', 'Order'),
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
}
