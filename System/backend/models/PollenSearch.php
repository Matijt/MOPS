<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Pollen;

/**
 * PollenSearch represents the model behind the search form of `backend\models\Pollen`.
 */
class PollenSearch extends Pollen
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idpollen', 'harvestWeek', 'order_idorder'], 'integer'],
            [['harvestDate', 'useWeek'], 'safe'],
            [['harvestMl', 'useMl', 'youHaveMl'], 'number'],
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
        $query = Pollen::find();

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


        if ($this->harvestDate) {
            $fromDate = substr($this->harvestDate, 0, 11);
            $untilDate = substr($this->harvestDate, 12, 12);
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $untilDate = date('Y-m-d', strtotime($untilDate));
            $query->andFilterWhere(['between', 'harvestDate', $fromDate, $untilDate]);
        }

        // grid filtering conditions
        $query->andFilterWhere([
            'idpollen' => $this->idpollen,
            'harvestWeek' => $this->harvestWeek,
            'harvestMl' => $this->harvestMl,
            'useWeek' => $this->useWeek,
            'useMl' => $this->useMl,
            'youHaveMl' => $this->youHaveMl,
            'order_idorder' => $this->order_idorder,
        ]);

        return $dataProvider;
    }
}
