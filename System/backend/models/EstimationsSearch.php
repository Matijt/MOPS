<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Estimations;

/**
 * EstimationsSearch represents the model behind the search form of `backend\models\Estimations`.
 */
class EstimationsSearch extends Estimations
{
    public $compartment;
    public $variety;
    public $orderKg;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idestimations', 'totalFemalesCount', 'totalPlantsCheked', 'inStock', 'fruitsHarvest', 'plantsTotal', 'pollinationDays', 'extraPollination', 'difference', 'order_idorder'], 'integer'],
            [['gramPerFruit', 'fruitsInPlant', 'gramsInPlant', 'totalHarvestS', 'fruitsAvgPerDay', 'fruitsToBeSetted', 'gramsToBeSetted', 'gramsRealToBeSetted', 'totalHarvest', 'avgGrsPlant', 'totalEstimatedProduction'], 'number'],
            [['fecha', 'variety', 'compartment', 'orderKg', 'LUser'], 'safe'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Estimations::find();

        // add conditions that should always apply here
        $query->joinWith('orderIdorder');
        $query->joinWith('orderIdorder.hybridIdHybr');
        $query->joinWith('orderIdorder.compartmentIdCompartment');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $dataProvider->sort->attributes['compartment'] = [
            'asc'  => ['compartment' => SORT_ASC],
            'desc' => ['compartment' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['variety'] = [
            'asc'  => ['variety' => SORT_ASC],
            'desc' => ['variety' => SORT_DESC],
        ];

        $dataProvider->sort->attributes['orderKg'] = [
            'asc'  => ['orderKg' => SORT_ASC],
            'desc' => ['orderKg' => SORT_DESC],
        ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idestimations' => $this->idestimations,
            'totalFemalesCount' => $this->totalFemalesCount,
            'totalPlantsCheked' => $this->totalPlantsCheked,
            'inStock' => $this->inStock,
            'fruitsHarvest' => $this->fruitsHarvest,
            'plantsTotal' => $this->plantsTotal,
            'gramPerFruit' => $this->gramPerFruit,
            'fruitsInPlant' => $this->fruitsInPlant,
            'gramsInPlant' => $this->gramsInPlant,
            'totalHarvestS' => $this->totalHarvestS,
            'pollinationDays' => $this->pollinationDays,
            'fruitsAvgPerDay' => $this->fruitsAvgPerDay,
            'extraPollination' => $this->extraPollination,
            'fruitsToBeSetted' => $this->fruitsToBeSetted,
            'gramsToBeSetted' => $this->gramsToBeSetted,
            'gramsRealToBeSetted' => $this->gramsRealToBeSetted,
            'totalHarvest' => $this->totalHarvest,
            'difference' => $this->difference,
            'avgGrsPlant' => $this->avgGrsPlant,
            'totalEstimatedProduction' => $this->totalEstimatedProduction,
            //'order_idorder' => $this->order_idorder,
            'fecha' => $this->fecha,
        ]);
        $query->andFilterWhere(['like', 'estimations.LUser', $this->LUser]);

        $query->andFilterWhere(['=', 'compartment.compNum', $this->order_idorder]);
        $query->andFilterWhere(['like', 'hybrid.variety', $this->variety]);
        $query->andFilterWhere(['like', 'order.orderKg', $this->orderKg]);

        return $dataProvider;
    }
}
