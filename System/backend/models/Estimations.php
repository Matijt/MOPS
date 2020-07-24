<?php

namespace backend\models;

use Yii;

/**
 * This is the model class for table "estimations".
 *
 * @property int $idestimations
 * @property int $totalFemalesCount
 * @property int $totalFemalesCount2
 * @property int $totalPlantsCheked
 * @property int $inStock
 * @property int $fruitsHarvest
 * @property int $plantsTotal
 * @property double $gramPerFruit
 * @property double $gramPerFruit2
 * @property double $gramPerFruit3
 * @property double $avgFruits1
 * @property double $fruitsInPlant
 * @property double $gramsInPlant
 * @property double $totalHarvestS
 * @property int $pollinationDays
 * @property double $fruitsAvgPerDay
 * @property int $extraPollination
 * @property double $fruitsToBeSetted
 * @property double $gramsToBeSetted
 * @property double $gramsRealToBeSetted
 * @property double $factorLess
 * @property double $totalHarvest
 * @property int $difference
 * @property double $avgGrsPlant
 * @property double $totalEstimatedProduction
 * @property int $order_idorder
 * @property string $fecha
 * @property string $fruitsEstimated1
 * @property string $fruitsEstimated2
 * @property string $fruitsEstimated3
 * @property string $gramsEstimated1
 * @property string $gramsEstimated2
 * @property string $gramsEstimated3
 * @property string $gramsSet1
 * @property string $gramsSet2
 * @property string $gramsSetFinal
 * @property double $avgFruits2
 * @property double $avgFruitsReal
 * @property double $LUser
 *
 * @property Order $orderIdorder
 */
class Estimations extends \yii\db\ActiveRecord
{
    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'estimations';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['totalFemalesCount', 'totalPlantsCheked', 'order_idorder', 'fecha'], 'required'],
            [['totalFemalesCount', 'totalPlantsCheked', 'inStock', 'fruitsHarvest', 'plantsTotal', 'pollinationDays', 'extraPollination', 'difference', 'order_idorder', 'factorLess'], 'integer'],
            [['gramPerFruit', 'gramPerFruit', 'gramsEstimated1', 'gramsEstimated2', 'gramsEstimated3',  'fruitsEstimated1', 'fruitsEstimated2', 'fruitsEstimated3', 'avgFruits1', 'avgFruits2', 'avgFruitsReal', 'gramPerFruit2', 'gramPerFruit3', 'fruitsInPlant', 'totalFemalesCount', 'totalFemalesCount2', 'gramsInPlant', 'totalHarvestS', 'fruitsAvgPerDay', 'fruitsToBeSetted', 'gramsToBeSetted', 'gramsRealToBeSetted', 'totalHarvest', 'avgGrsPlant', 'totalEstimatedProduction', 'avgFruits1', 'gramsSet1', 'gramsSet2', 'gramsSetFinal'], 'number'],
            [['fecha', 'LUser'], 'safe'],
            [['order_idorder'], 'exist', 'skipOnError' => true, 'targetClass' => Order::className(), 'targetAttribute' => ['order_idorder' => 'idorder']],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'idestimations' => Yii::t('app', 'Idestimations'),
            'totalFemalesCount' => Yii::t('app', 'Total Fruits Count 1'),
            'totalFemalesCount2' => Yii::t('app', 'Total Fruits Count 2'),
            'totalPlantsCheked' => Yii::t('app', 'Total Plants Cheked'),
            'inStock' => Yii::t('app', 'In Stock'),
            'fruitsHarvest' => Yii::t('app', 'Fruits Harvest'),
            'plantsTotal' => Yii::t('app', 'Plants Total'),
            'gramPerFruit' => Yii::t('app', 'Gram Per Fruit 1'),
            'gramPerFruit2' => Yii::t('app', 'Gram Per Fruit 2'),
            'gramPerFruit3' => Yii::t('app', 'Gram Per Fruit 3'),
            'avgFruits1' => Yii::t('app', 'Avg Fruits 1'),
            'fruitsInPlant' => Yii::t('app', 'Fruits In Plant'),
            'gramsInPlant' => Yii::t('app', 'Grams In Plant'),
            'totalHarvestS' => Yii::t('app', 'Total Harvest S'),
            'pollinationDays' => Yii::t('app', 'Pollination Days'),
            'fruitsAvgPerDay' => Yii::t('app', 'Fruits Avg Per Day'),
            'extraPollination' => Yii::t('app', 'Extra Pollination'),
            'fruitsToBeSetted' => Yii::t('app', 'Fruits To Be Setted'),
            'gramsToBeSetted' => Yii::t('app', 'Grams To Be Setted'),
            'gramsRealToBeSetted' => Yii::t('app', 'Grams Real To Be Setted'),
            'totalHarvest' => Yii::t('app', 'Total Harvest'),
            'difference' => Yii::t('app', 'Difference'),
            'avgGrsPlant' => Yii::t('app', 'Grams/Plant'),
            'totalEstimatedProduction' => Yii::t('app', 'Total Estimated Production'),
            'order_idorder' => Yii::t('app', 'Order'),
            'fecha' => Yii::t('app', 'Date'),
            'gramsEstimated1' => Yii::t('app', 'Grams Estimated 1'),
            'gramsEstimated2' => Yii::t('app', 'Grams Estimated 2'),
            'gramsEstimated3' => Yii::t('app', 'Grams Estimated 3'),
            'fruitsEstimated1' => Yii::t('app', 'Fruits Estimated 1'),
            'fruitsEstimated2' => Yii::t('app', 'Fruits Estimated 2'),
            'fruitsEstimated3' => Yii::t('app', 'Fruits Estimated 3'),
            'gramsSet1' => Yii::t('app', 'grams Real 1'),
            'gramsSet2' => Yii::t('app', 'grams Real 2'),
            'gramsSetFinal' => Yii::t('app', 'grams Real Final'),
            'avgFruits2' => Yii::t('app', 'Avg Fruits 2'),
            'avgFruitsReal' => Yii::t('app', 'Avg Fruits Real'),
            'factorLess' => Yii::t('app', 'Factor less'),
            'LUser' => Yii::t('app', 'Last User'),
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getOrderIdorder()
    {
        return $this->hasOne(Order::className(), ['idorder' => 'order_idorder']);
    }
}
