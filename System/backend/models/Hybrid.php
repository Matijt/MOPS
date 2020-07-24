<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "hybrid".
 *
 * @property integer $idHybrid
 * @property string $variety
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
 * @property string $remarks
 * @property integer $Crop_idcrops
 * @property integer $Father_idFather
 * @property integer $Mother_idMother
 * @property integer $delete
 *
 * @property Histcrop[] $histcrops
 * @property Crop $cropIdcrops
 * @property Father $fatherIdFather
 * @property Mother $motherIdMother
 * @property Mother $tsw
 * @property Order[] $orders
 */
class Hybrid extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'hybrid';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['variety', 'Crop_idcrops', 'Father_idFather', 'Mother_idMother'], 'required'],
            [['sowingFemale', 'transplantingMale', 'transplantingFemale', 'pollenColectF', 'pollenColectU', 'pollinitionF', 'pollinitionU', 'harvestF', 'harvestU', 'steamDesinfection', 'Crop_idcrops', 'Father_idFather', 'Mother_idMother', 'delete'], 'integer'],
            [['tsw'], 'number'],
            [['remarks'], 'string'],
            [['variety'], 'string', 'max' => 10],
            [['Crop_idcrops'], 'exist', 'skipOnError' => true, 'targetClass' => Crop::className(), 'targetAttribute' => ['Crop_idcrops' => 'idcrops']],
            [['Father_idFather'], 'exist', 'skipOnError' => true, 'targetClass' => Father::className(), 'targetAttribute' => ['Father_idFather' => 'idFather']],
            [['Mother_idMother'], 'exist', 'skipOnError' => true, 'targetClass' => Mother::className(), 'targetAttribute' => ['Mother_idMother' => 'idMother']],
            [['variety'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idHybrid' => Yii::t('app', 'Id Hybrid'),
            'variety' => Yii::t('app', 'Variety'),
            'sowingFemale' => Yii::t('app', 'Sowing Female'),
            'transplantingMale' => Yii::t('app', 'Transplanting Male'),
            'transplantingFemale' => Yii::t('app', 'Transplanting Female'),
            'pollenColectF' => Yii::t('app', 'Pollen Harvest F'),
            'pollenColectU' => Yii::t('app', 'Pollen Harvest U'),
            'pollinitionF' => Yii::t('app', 'Pollination F'),
            'pollinitionU' => Yii::t('app', 'Pollination U'),
            'harvestF' => Yii::t('app', 'Harvest F'),
            'harvestU' => Yii::t('app', 'Harvest U'),
            'steamDesinfection' => Yii::t('app', 'Steam Desinfection'),
            'remarks' => Yii::t('app', 'Remarks'),
            'Crop_idcrops' => Yii::t('app', 'Crop'),
            'Father_idFather' => Yii::t('app', 'Father'),
            'Mother_idMother' => Yii::t('app', 'Mother'),
            'tsw' => Yii::t('app', 'TSW'),
            'delete' => Yii::t('app', 'Deleted'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistcrops()
    {
        return $this->hasMany(Histcrop::className(), ['Hybrid_idHybrid' => 'idHybrid']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCropIdcrops()
    {
        return $this->hasOne(Crop::className(), ['idcrops' => 'Crop_idcrops']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getFatherIdFather()
    {
        return $this->hasOne(Father::className(), ['idFather' => 'Father_idFather']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getMotherIdMother()
    {
        return $this->hasOne(Mother::className(), ['idMother' => 'Mother_idMother']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['Hybrid_idHybrid' => 'idHybrid']);
    }
}
