<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "history".
 *
 * @property integer $idHistory
 * @property string $content
 * @property string $date
 */
class History extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'history';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['content'], 'string'],
            [['date'], 'safe'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'idHistory' => Yii::t('app', 'Id History'),
            'content' => Yii::t('app', 'Content'),
            'date' => Yii::t('app', 'Date'),
        ];
    }
}
