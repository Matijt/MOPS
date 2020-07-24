<?php

namespace backend\models;

use backend\models\StocklistHasOrder;
use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * StocklistHasOrderSearch represents the model behind the search form of `backend\models\StocklistHasOrder`.
 */
class StocklistHasOrderSearch extends StocklistHasOrder
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idstocklist_has_order', 'stocklist_idstocklist', 'order_idorder', 'totalNumberOfFruitsHarvested', 'totalWetSeedWeight', 'totalNumberOfBags', 'totalInStock', 'totalShipped'], 'integer'],
            [['totalAvarageWeightOfSeedsPF', 'avarageGP'], 'number'],
            [['totalDrySeedWeight',
            ], 'safe'],
        ];
    }

    /**
     * @inheritdoc
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
        $query = StocklistHasOrder::find();
//        $query = StocklistHasOrder::find()->groupBy(['`order_idorder` ASC']);

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('stocklistIdstocklist');
        $query->joinWith('orderIdorder');
        $query->joinWith('orderIdorder.compartmentIdCompartment');
        $query->joinWith('orderIdorder.hybridIdHybr');


        $query
            ->andFilterWhere(['like', 'hybrid.variety', $this->totalDrySeedWeight,])
            ->andFilterWhere(['like','stocklist.harvestNumber',$this->idstocklist_has_order,])
            ->andFilterWhere(['=','order.numCrop',$this->totalShipped])
            ->andFilterWhere(['like','compartment.compNum',$this->order_idorder])
            ->andFilterWhere(['like','totalNumberOfFruitsHarvested',$this->totalNumberOfFruitsHarvested])
            ->andFilterWhere(['like', 'stocklist_has_order.LUser', $this->LUser])
            ->distinct();
        ;

        return $dataProvider;
    }
}
