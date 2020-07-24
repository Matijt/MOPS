<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Father;

/**
 * FatherSearch represents the model behind the search form of `backend\models\Father`.
 */
class FatherSearch extends Father
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idFather', 'steril', 'germination', 'tsw'], 'integer'],
            [['variety', 'remarks'], 'safe'],
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
        $query = Father::find();

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
            'idFather' => $this->idFather,
            'steril' => $this->steril,
            'germination' => $this->germination,
            'tsw' => $this->tsw,
        ]);

        $query->andFilterWhere(['like', 'variety', $this->variety])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
