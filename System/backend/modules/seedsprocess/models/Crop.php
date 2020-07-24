<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "crop".
 *
 * @property integer $idcrops
 * @property string $crop
 * @property integer $sowingFemale
 * @property integer $transplantingMale
 * @property integer $transplantingFemale
 * @property integer $pollenColectF
 * @property integer $pollenColectU
 * @property integer $pollinitionF
 * @property integer $pollinitionU
 * @property integer $harvestF
 * @property integer $harvestU
 * @property integer $steamDesinfection
 * @property integer $durationOfTheCrop
 * @property string $remarks
 *
 * @property Hybrid[] $hybrs
 */
class Crop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'crop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sowingFemale', 'transplantingMale', 'transplantingFemale', 'pollenColectF', 'pollenColectU', 'pollinitionF', 'pollinitionU', 'harvestF', 'harvestU', 'steamDesinfection', 'durationOfTheCrop'], 'integer'],
            [['remarks'], 'string'],
            [['crop'], 'string', 'max' => 45],
            [['crop'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcrops' => Yii::t('app', 'Idcrops'),
            'crop' => Yii::t('app', 'Crop'),
            'sowingFemale' => Yii::t('app', 'Sowing Female'),
            'transplantingMale' => Yii::t('app', 'Transplanting Male'),
            'transplantingFemale' => Yii::t('app', 'Transplanting Female'),
            'pollenColectF' => Yii::t('app', 'Pollen Colect F'),
            'pollenColectU' => Yii::t('app', 'Pollen Colect U'),
            'pollinitionF' => Yii::t('app', 'Pollinition F'),
            'pollinitionU' => Yii::t('app', 'Pollinition U'),
            'harvestF' => Yii::t('app', 'Harvest F'),
            'harvestU' => Yii::t('app', 'Harvest U'),
            'steamDesinfection' => Yii::t('app', 'Steam Desinfection'),
            'durationOfTheCrop' => Yii::t('app', 'Duration Of The Crop'),
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHybrs()
    {
        return $this->hasMany(Hybrid::className(), ['Crop_idcrops' => 'idcrops']);
    }
}
