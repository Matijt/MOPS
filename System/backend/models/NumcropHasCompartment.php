<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "numcrop_has_compartment".
 *
 * @property int $numcrop_cropnum
 * @property int $compartment_idCompartment
 * @property string $createDate
 * @property string $freeDate
 * @property string $lastUpdatedDate
 * @property int $rowsOccupied
 * @property int $rowsLeft
 * @property int $crop_idcrops
 * @property string $fecha_cancelado
 * @property string $estado
 *
 * @property Compartment $compartmentIdCompartment
 * @property Crop $cropIdcrops
 * @property Numcrop $numcropCropnum
 * @property OrderHasNumcropHasCompartment[] $orderHasNumcropHasCompartments
 * @property Order[] $orderIdorders
 */
class NumcropHasCompartment extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'numcrop_has_compartment';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numcrop_cropnum', 'compartment_idCompartment', 'crop_idcrops'], 'required'],
            [['numcrop_cropnum', 'compartment_idCompartment', 'rowsOccupied', 'rowsLeft', 'crop_idcrops'], 'integer'],
            [['createDate', 'freeDate', 'lastUpdatedDate', 'fecha_cancelado', 'estado'], 'safe'],
            [['compartment_idCompartment'], 'exist', 'skipOnError' => true, 'targetClass' => Compartment::className(), 'targetAttribute' => ['compartment_idCompartment' => 'idCompartment']],
            [['crop_idcrops'], 'exist', 'skipOnError' => true, 'targetClass' => Crop::className(), 'targetAttribute' => ['crop_idcrops' => 'idcrops']],
            [['numcrop_cropnum'], 'exist', 'skipOnError' => true, 'targetClass' => Numcrop::className(), 'targetAttribute' => ['numcrop_cropnum' => 'cropnum']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'numcrop_cropnum' => Yii::t('app', 'Crop number'),
            'compartment_idCompartment' => Yii::t('app', 'Compartment'),
            'createDate' => Yii::t('app', 'Create Date'),
            'freeDate' => Yii::t('app', 'Free Date'),
            'lastUpdatedDate' => Yii::t('app', 'Last Updated Date'),
            'rowsOccupied' => Yii::t('app', 'Rows Occupied'),
            'rowsLeft' => Yii::t('app', 'Rows Left'),
            'crop_idcrops' => Yii::t('app', 'Crop'),
            'fecha_cancelado' => Yii::t('app', 'Canceled Date'),
            'estado' => Yii::t('app', 'Active'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCompartmentIdCompartment()
    {
        return $this->hasOne(Compartment::className(), ['idCompartment' => 'compartment_idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getCropIdcrops()
    {
        return $this->hasOne(Crop::className(), ['idcrops' => 'crop_idcrops']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNumcropCropnum()
    {
        return $this->hasOne(Numcrop::className(), ['cropnum' => 'numcrop_cropnum']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderHasNumcropHasCompartments()
    {
        return $this->hasMany(OrderHasNumcropHasCompartment::className(), ['numcrop_has_compartment_numcrop_cropnum' => 'numcrop_cropnum', 'numcrop_has_compartment_compartment_idCompartment' => 'compartment_idCompartment']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorders()
    {
        return $this->hasMany(Order::className(), ['idorder' => 'order_idorder'])->viaTable('order_has_numcrop_has_compartment', ['numcrop_has_compartment_numcrop_cropnum' => 'numcrop_cropnum', 'numcrop_has_compartment_compartment_idCompartment' => 'compartment_idCompartment']);
    }

    public function getfullName()
    {
        $genial = 'Crop: '.$this->numcrop_cropnum.' Compartment: '.$this->compartmentIdCompartment->compNum.", Rows left: ".$this->rowsLeft.".";
        return $genial;
    }

    public function getfullId()
    {
        $genial = $this->compartment_idCompartment.','.$this->numcrop_cropnum;
        return $genial;
    }
}
