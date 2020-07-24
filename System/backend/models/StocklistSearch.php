<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Stocklist;

/**
 * StocklistSearch represents the model behind the search form of `backend\models\Stocklist`.
 */
class StocklistSearch extends Stocklist
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idstocklist', 'harvestNumber', 'numberOfFruitsHarvested', 'wetSeedWeight', 'drySeedWeight', 'numberOfBags', 'cartonNo'], 'integer'],
            [['harvestDate', 'cleaningDate', 'shipmentDate', 'packingListDescription', 'remarksSeeds', 'destroyed', 'eol', 'status', 'LUser'], 'safe'],
            [['avgWeightOfSeedPF', 'moisture', 'tsw'], 'number'],
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
        $query = Stocklist::find();

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

        // grid filtering conditions
        $query->andFilterWhere([
            'idstocklist' => $this->idstocklist,
            'harvestNumber' => $this->harvestNumber,
            'harvestDate' => $this->harvestDate,
            'numberOfFruitsHarvested' => $this->numberOfFruitsHarvested,
            'cleaningDate' => $this->cleaningDate,
            'wetSeedWeight' => $this->wetSeedWeight,
            'drySeedWeight' => $this->drySeedWeight,
            'avgWeightOfSeedPF' => $this->avgWeightOfSeedPF,
            'numberOfBags' => $this->numberOfBags,
            'cartonNo' => $this->cartonNo,
            'shipmentDate' => $this->shipmentDate,
            'moisture' => $this->moisture,
            'tsw' => $this->tsw,
        ]);

        $query->andFilterWhere(['like', 'packingListDescription', $this->packingListDescription])
            ->andFilterWhere(['like', 'remarksSeeds', $this->remarksSeeds])
            ->andFilterWhere(['like', 'destroyed', $this->destroyed])
            ->andFilterWhere(['like', 'eol', $this->eol])
            ->andFilterWhere(['like', 'status', $this->status])
            ->andFilterWhere(['like', 'stocklist.LUser', $this->LUser]);

        return $dataProvider;
    }
}
