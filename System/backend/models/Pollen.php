<?php

namespace backend\models;

use Yii;
use yii\behaviors\TimestampBehavior;
use yii\behaviors\BlameableBehavior;
use mootensai\behaviors\UUIDBehavior;

/**
 * This is the base model class for table "pollen".
 *
 * @property integer $idpollen
 * @property integer $harvestWeek
 * @property string $harvestDate
 * @property string $harvestMl
 * @property string $useWeek
 * @property string $useMl
 * @property string $youHaveMl
 * @property integer $order_idorder
 *
 * @property \backend\models\Order $orderIdorder
 */
class Pollen extends \yii\db\ActiveRecord
{
    use \mootensai\relation\RelationTrait;

    /**
     * @inheritdoc
     */
    public $file;
    public function rules()
    {
        return [
            [['harvestWeek', 'order_idorder'], 'integer'],
            [['harvestDate', 'useWeek'], 'safe'],
            [['harvestMl', 'useMl', 'youHaveMl'], 'number'],
            [['order_idorder'], 'required'],
            [['file'], 'file']
        ];
    }
    
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'pollen';
    }

    /**
     * 
     * @return string
     * overwrite function optimisticLock
     * return string name of field are used to stored optimistic lock 
     * 
     */

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
            'order_idorder' => Yii::t('app', 'Order information'),
        ];
    }
    
    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(\backend\models\Order::className(), ['idorder' => 'order_idorder']);
    }
    
/**
     * @inheritdoc
     * @return array mixed
     */

    /**
     * @inheritdoc
     * @return \app\models\PollenQuery the active query used by this AR class.
     */
    public static function find()
    {
        return new \app\models\PollenQuery(get_called_class());
    }
}
