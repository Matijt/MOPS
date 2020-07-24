<?php

namespace backend\modules\seedsprocess\models;

use Yii;

/**
 * This is the model class for table "compartment".
 *
 * @property int $idCompartment
 * @property int $compNum
 * @property int $rowsNum
 * @property double $grossSurface
 * @property double $netSurface
 * @property double $grossLength
 * @property double $netLength
 * @property int $width
 * @property string $remarks
 *
 * @property Historialcomp[] $historialcomps
 * @property NumcropHasCompartment[] $numcropHasCompartments
 * @property Numcrop[] $numcropCropnums
 * @property Order[] $orders
 */
class Compartment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'compartment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['compNum'], 'required'],
            [['compNum', 'rowsNum', 'width'], 'integer'],
            [['grossSurface', 'netSurface', 'grossLength', 'netLength'], 'number'],
            [['remarks'], 'string'],
            [['compNum'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idCompartment' => Yii::t('app', 'Id Compartment'),
            'compNum' => Yii::t('app', 'Comp Num'),
            'rowsNum' => Yii::t('app', 'Rows Num'),
            'grossSurface' => Yii::t('app', 'Gross Surface'),
            'netSurface' => Yii::t('app', 'Net Surface'),
            'grossLength' => Yii::t('app', 'Gross Length'),
            'netLength' => Yii::t('app', 'Net Length'),
            'width' => Yii::t('app', 'Width'),
            'remarks' => Yii::t('app', 'Remarks'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getHistorialcomps()
    {
        return $this->hasMany(Historialcomp::className(), ['compartment_idCompartment' => 'idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumcropHasCompartments()
    {
        return $this->hasMany(NumcropHasCompartment::className(), ['compartment_idCompartment' => 'idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumcropCropnums()
    {
        return $this->hasMany(Numcrop::className(), ['cropnum' => 'numcrop_cropnum'])->viaTable('numcrop_has_compartment', ['compartment_idCompartment' => 'idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrders()
    {
        return $this->hasMany(Order::className(), ['compartment_idCompartment' => 'idCompartment']);
    }
}
