<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "mother".
 *
 * @property integer $idMother
 * @property string $variety
 * @property integer $steril
 * @property integer $germination
 * @property integer $tsw
 * @property string $gP
 * @property double $ratio
 * @property string $remarks
 *
 * @property Hybrid[] $hybrs
 */
class Mother extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'mother';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variety', 'gP'], 'required'],
            [['steril', 'germination', 'tsw'], 'integer'],
            [['gP', 'ratio'], 'number'],
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
            'idMother' => Yii::t('app', 'Id Mother'),
            'variety' => Yii::t('app', 'Variety'),
            'steril' => Yii::t('app', 'Steril'),
            'germination' => Yii::t('app', 'Germination'),
            'tsw' => Yii::t('app', 'Tsw'),
            'gP' => Yii::t('app', 'G P'),
            'ratio' => Yii::t('app', 'Ratio'),
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHybrs()
    {
        return $this->hasMany(Hybrid::className(), ['Mother_idMother' => 'idMother']);
    }
}
