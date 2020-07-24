<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "germination".
 *
 * @property integer $idGermination
 * @property string $maleOrfemale
 * @property string $variety
 * @property string $description
 * @property string $updatedAt
 */
class Germination extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'germination';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['description'], 'string'],
            [['updatedAt'], 'safe'],
            [['maleOrfemale', 'variety'], 'string', 'max' => 45],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idGermination' => Yii::t('app', 'Id Germination'),
            'maleOrfemale' => Yii::t('app', 'Male Or Female'),
            'variety' => Yii::t('app', 'Variety'),
            'description' => Yii::t('app', 'Description'),
            'updatedAt' => Yii::t('app', 'Updated At'),
        ];
    }
}
