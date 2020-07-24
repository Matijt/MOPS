<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Historialnursery;

use yii\bootstrap\Modal;
use yii\helpers\Url;

/**
 * HistorialnurserySearch represents the model behind the search form of `backend\models\Historialnursery`.
 */
class HistorialnurserySearch extends Historialnursery
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idhistorialnursery', 'nursery_idnursery'], 'integer'],
            [['title', 'content', 'date'], 'safe'],
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
        $query = Historialnursery::find();

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

        $query->joinWith('nurseryIdnursery');

        if($this->date) {
            $this->date = date('Y-m-d', strtotime($this->date));
        }
        // grid filtering conditions
        $query->andFilterWhere(['like', 'content', $this->content]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'nursery.numcompartment', $this->nursery_idnursery])
            ->andFilterWhere(['like', 'date', $this->date]);

        IF($this->date) {
            $this->date = date('d-m-Y', strtotime($this->date));
        }
        return $dataProvider;
    }
}
