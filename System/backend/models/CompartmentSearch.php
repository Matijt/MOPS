<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Compartment;

/**
 * CompartmentSearch represents the model behind the search form of `backend\models\Compartment`.
 */
class CompartmentSearch extends Compartment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idCompartment', 'compNum', 'rowsNum', 'width'], 'integer'],
            [['grossSurface', 'netSurface', 'grossLength', 'netLength'], 'number'],
            [['remarks'], 'safe'],
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
        $query = Compartment::find();

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
            'idCompartment' => $this->idCompartment,
            'compNum' => $this->compNum,
            'rowsNum' => $this->rowsNum,
            'grossSurface' => $this->grossSurface,
            'netSurface' => $this->netSurface,
            'grossLength' => $this->grossLength,
            'netLength' => $this->netLength,
            'width' => $this->width,
        ]);

        $query->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
