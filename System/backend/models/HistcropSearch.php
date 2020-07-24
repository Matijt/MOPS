<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Histcrop;

/**
 * HistcropSearch represents the model behind the search form of `backend\models\Histcrop`.
 */
class HistcropSearch extends Histcrop
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idhistCrop', 'Hybrid_idHybrid', 'days'], 'integer'],
            [['createDate', 'proces'], 'safe'],
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
        $query = Histcrop::find();

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
            'idhistCrop' => $this->idhistCrop,
            'Hybrid_idHybrid' => $this->Hybrid_idHybrid,
            'days' => $this->days,
            'createDate' => $this->createDate,
        ]);

        $query->andFilterWhere(['like', 'proces', $this->proces]);

        return $dataProvider;
    }
}
