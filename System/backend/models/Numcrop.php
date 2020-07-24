<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "numcrop".
 *
 * @property integer $cropnum
 *
 * @property NumcropHasCompartment[] $numcropHasCompartments
 * @property Compartment[] $compartmentIdCompartments
 */
class Numcrop extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'numcrop';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'cropnum' => Yii::t('app', 'Cropnum'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumcropHasCompartments()
    {
        return $this->hasMany(NumcropHasCompartment::className(), ['numcrop_cropnum' => 'cropnum']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompartmentIdCompartments()
    {
        return $this->hasMany(Compartment::className(), ['idCompartment' => 'compartment_idCompartment'])->viaTable('numcrop_has_compartment', ['numcrop_cropnum' => 'cropnum']);
    }
}
