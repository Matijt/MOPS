<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "historialnursery".
 *
 * @property int $idhistorialnursery
 * @property string $title
 * @property string $content
 * @property string $date
 * @property int $nursery_idnursery
 * @property int $crop
 *
 * @property Nursery $nurseryIdnursery
 */
class Historialnursery extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'historialnursery';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['title', 'content', 'date', 'nursery_idnursery'], 'required'],
            [['content'], 'string'],
            [['date'], 'safe'],
            [['nursery_idnursery', 'crop'], 'integer'],
            [['title'], 'string', 'max' => 255],
            [['nursery_idnursery'], 'exist', 'skipOnError' => true, 'targetClass' => Nursery::className(), 'targetAttribute' => ['nursery_idnursery' => 'idnursery']],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idhistorialnursery' => Yii::t('app', 'id'),
            'title' => Yii::t('app', 'Title'),
            'content' => Yii::t('app', 'Content'),
            'date' => Yii::t('app', 'Date'),
            'crop' => Yii::t('app', 'Crop'),
            'nursery_idnursery' => Yii::t('app', 'Nursery compartment'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getNurseryIdnursery()
    {
        return $this->hasOne(Nursery::className(), ['idnursery' => 'nursery_idnursery']);
    }
}
