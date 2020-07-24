<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "trial".
 *
 * @property int $id_trial
 * @property string $reason
 * @property string $description
 * @property string $observations
 * @property int $numRows
 * @property int $compartment_idCompartment
 * @property int $numCrop
 *
 * @property Order[] $orders
 * @property Numcrop $numCrop0
 * @property Compartment $compartmentIdCompartment
 */
class Trial extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'trial';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['reason', 'numRows', 'compartment_idCompartment', 'numCrop'], 'required'],
            [['description', 'observations'], 'string'],
            [['numRows', 'compartment_idCompartment', 'numCrop'], 'integer'],
            [['reason'], 'string', 'max' => 50],
            [['numCrop'], 'exist', 'skipOnError' => true, 'targetClass' => Numcrop::className(), 'targetAttribute' => ['numCrop' => 'cropnum']],
            [['compartment_idCompartment'], 'exist', 'skipOnError' => true, 'targetClass' => Compartment::className(), 'targetAttribute' => ['compartment_idCompartment' => 'idCompartment']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id_trial' => Yii::t('app', 'Id Trial'),
            'reason' => Yii::t('app', 'Reason'),
            'description' => Yii::t('app', 'Description'),
            'observations' => Yii::t('app', 'Observations'),
            'numRows' => Yii::t('app', 'Num Rows'),
            'compartment_idCompartment' => Yii::t('app', 'Compartment'),
            'numCrop' => Yii::t('app', 'Num Crop'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['trial_id' => 'id_trial']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumCrop0()
    {
        return $this->hasOne(Numcrop::className(), ['cropnum' => 'numCrop']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompartmentIdCompartment()
    {
        return $this->hasOne(Compartment::className(), ['idCompartment' => 'compartment_idCompartment']);
    }
}
