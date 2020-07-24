<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "color".
 *
 * @property int $idcolor
 * @property string $color
 * @property string $proceso
 *
 * @property Event[] $events
 */
class Color extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'color';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['color'], 'string', 'max' => 15],
            [['proceso'], 'string', 'max' => 45],
            [['color'], 'unique'],
            [['proceso'], 'unique'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idcolor' => Yii::t('app', 'Idcolor'),
            'color' => Yii::t('app', 'Color'),
            'proceso' => Yii::t('app', 'Proceso'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getEvents()
    {
        return $this->hasMany(Event::className(), ['color_idcolor' => 'idcolor']);
    }
}
