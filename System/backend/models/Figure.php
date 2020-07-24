<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "figure".
 *
 * @property int $id
 * @property string $figure
 *
 * @property Registrynursery[] $registrynurseries
 */
class Figure extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'figure';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['figure'], 'string', 'max' => 45],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app', 'ID'),
            'figure' => Yii::t('app', 'Figure'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getRegistrynurseries()
    {
        return $this->hasMany(Registrynursery::className(), ['figure_id' => 'id']);
    }
}
