<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Germination;

/**
 * GerminationSearch represents the model behind the search form of `backend\models\Germination`.
 */
class GerminationSearch extends Germination
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idGermination'], 'integer'],
            [['maleOrfemale', 'variety', 'description', 'updatedAt'], 'safe'],
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
        $query = Germination::find();

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
            'idGermination' => $this->idGermination,
        ]);
        if($this->updatedAt) {
            $this->updatedAt = date('Y-m-d', strtotime($this->updatedAt));
        }

        $query->andFilterWhere(['like', 'maleOrfemale', $this->maleOrfemale])
            ->andFilterWhere(['like', 'variety', $this->variety])
            ->andFilterWhere(['like', 'description', $this->description])
            ->andFilterWhere(['like', 'updatedAt', $this->updatedAt]);
        IF($this->updatedAt) {
            $this->updatedAt = date('d-m-Y', strtotime($this->updatedAt));
        }

        return $dataProvider;
    }
}
