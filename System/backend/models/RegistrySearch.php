<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Registry;

/**
 * RegistrySearch represents the model behind the search form of `backend\models\Registry`.
 */
class RegistrySearch extends Registry
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['idregistry', 'quantity', 'order_idorder', 'numRow'], 'integer'],
            [['LUser'], 'safe'],
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
        $query = Registry::find();

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
            'idregistry' => $this->idregistry,
            'quantity' => $this->quantity,
            'numRow' => $this->numRow,
            'order_idorder' => $this->order_idorder,
        ]);
        $query->andFilterWhere(['like', 'registry.LUser', $this->LUser]);

        return $dataProvider;
    }
}
