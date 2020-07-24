<?php

namespace backend\modules\seedsproces\models;

use Yii;

/**
 * This is the model class for table "event".
 *
 * @property int $idevent
 * @property string $title
 * @property string $description
 * @property string $startDate
 * @property string $endDate
 * @property int $order_idorder
 * @property int $color_idcolor
 *
 * @property Color $colorIdcolor
 * @property Order $orderIdorder
 */
class Event extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'event';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['startDate', 'endDate'], 'safe'],
            [['order_idorder', 'color_idcolor'], 'required'],
            [['order_idorder', 'color_idcolor'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['color_idcolor'], 'exist', 'skipOnError' => true, 'targetClass' => Color::className(), 'targetAttribute' => ['color_idcolor' => 'idcolor']],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idevent' => Yii::t('app', 'Idevent'),
            'title' => Yii::t('app', 'Title'),
            'description' => Yii::t('app', 'Description'),
            'startDate' => Yii::t('app', 'Start Date'),
            'endDate' => Yii::t('app', 'End Date'),
            'order_idorder' => Yii::t('app', 'Order Idorder'),
            'color_idcolor' => Yii::t('app', 'Color Idcolor'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getColorIdcolor()
    {
        return $this->hasOne(Color::className(), ['idcolor' => 'color_idcolor']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(Order::className(), ['idorder' => 'order_idorder']);
    }
}
