<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "histcrop".
 *
 * @property integer $idhistCrop
 * @property integer $Hybrid_idHybrid
 * @property integer $days
 * @property string $createDate
 * @property string $proces
 *
 * @property Hybrid $hybridIdHybr
 */
class Histcrop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'histcrop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['Hybrid_idHybrid', 'days', 'proces'], 'required'],
            [['Hybrid_idHybrid', 'days'], 'integer'],
            [['createDate'], 'safe'],
            [['proces'], 'string', 'max' => 255],
            [['Hybrid_idHybrid'], 'exist', 'skipOnError' => true, 'targetClass' => Hybrid::className(), 'targetAttribute' => ['Hybrid_idHybrid' => 'idHybrid']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idhistCrop' => Yii::t('app', 'Idhist Crop'),
            'Hybrid_idHybrid' => Yii::t('app', 'Hybrid Id Hybrid'),
            'days' => Yii::t('app', 'Days'),
            'createDate' => Yii::t('app', 'Create Date'),
            'proces' => Yii::t('app', 'Proces'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHybridIdHybr()
    {
        return $this->hasOne(Hybrid::className(), ['idHybrid' => 'Hybrid_idHybrid']);
    }
}
