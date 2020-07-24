<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "father".
 *
 * @property int $idFather
 * @property string $variety
 * @property int $steril
 * @property int $germination
 * @property int $pollenProduction
 * @property int $tsw
 * @property int $pollenUsed
 * @property int $pollenLeft
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
            [['variety', 'pollenProduction', 'pollenUsed', 'pollenLeft'], 'required'],
            [['steril', 'germination', 'pollenProduction', 'tsw', 'pollenUsed', 'pollenLeft'], 'integer'],
            [['remarks'], 'string'],
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
            'pollenProduction' => Yii::t('app', 'Pollen Production'),
            'tsw' => Yii::t('app', 'Tsw'),
            'pollenUsed' => Yii::t('app', 'Pollen Used'),
            'pollenLeft' => Yii::t('app', 'Pollen Left'),
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
