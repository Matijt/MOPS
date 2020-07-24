<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Historialcomp;
use nerburish\daterangevalidator\DateRangeValidator;


/**
 * HistorialcompSearch represents the model behind the search form of `backend\models\Historialcomp`.
 */
class HistorialcompSearch extends Historialcomp
{
    /**
     * @inheritdoc
     */


    public function rules()
    {
        return [
            [['idHistorialcomp', 'compartment_idCompartment'], 'integer'],
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
        $query = Historialcomp::find();

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

        $query->joinWith('compartmentIdCompartment');

        // grid filtering conditions
        $query->andFilterWhere([
            'idHistorialcomp' => $this->idHistorialcomp,
        ]);
        if ($this->date) {
            $fromDate = substr($this->date, 0, 11);
            $untilDate = substr($this->date, 12, 12);
            $fromDate = date('Y-m-d', strtotime($fromDate));
            $untilDate = date('Y-m-d', strtotime($untilDate));
            $query->andFilterWhere(['between', 'date', $fromDate, $untilDate]);
        }

        $query->andFilterWhere(['like', 'content', $this->content]);
        $query->andFilterWhere(['like', 'title', $this->title]);
        $query->andFilterWhere(['like', 'compartment.compnum', $this->compartment_idCompartment]);
//       die;
        return $dataProvider;

    }
}
