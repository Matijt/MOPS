<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "father".
 *
 * @property integer $idFather
 * @property string $variety
 * @property integer $steril
 * @property integer $germination
 * @property integer $tsw
 * @property string $remarks
 *
 * @property Hybrid[] $hybrs
 */
class Father extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'father';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variety'], 'required'],
            [['steril', 'germination'], 'integer'],
            [['remarks'], 'string'],
            [['tsw'], 'number'],
            [['variety'], 'string', 'max' => 7],
            [['variety'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idFather' => Yii::t('app', 'Id Father'),
            'variety' => Yii::t('app', 'Variety'),
            'steril' => Yii::t('app', 'Steril'),
            'germination' => Yii::t('app', 'Germination'),
            'tsw' => Yii::t('app', 'Tsw'),
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHybrs()
    {
        return $this->hasMany(Hybrid::className(), ['Father_idFather' => 'idFather']);
    }
}
