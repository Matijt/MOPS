<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\NumcropHasCompartment;

/**
 * NumcropHasCompartmentSearch represents the model behind the search form of `backend\models\NumcropHasCompartment`.
 */
class NumcropHasCompartmentSearch extends NumcropHasCompartment
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numcrop_cropnum', 'compartment_idCompartment', 'rowsOccupied', 'rowsLeft'], 'integer'],
            [['createDate', 'freeDate', 'lastUpdatedDate', 'crop_idcrops', 'fecha_cancelado', 'estado'], 'safe'],
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
        $query = NumcropHasCompartment::find();

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
        $query->joinWith('cropIdcrops');

        // grid filtering conditions
        $query->andFilterWhere([
            'numcrop_cropnum' => $this->numcrop_cropnum,
            'createDate' => $this->createDate,
            'freeDate' => $this->freeDate,
            'lastUpdatedDate' => $this->lastUpdatedDate,
            'rowsOccupied' => $this->rowsOccupied,
            'rowsLeft' => $this->rowsLeft,
        ]);

        $query->andFilterWhere(['like', 'compartment.compnum', $this->compartment_idCompartment]);
        $query->andFilterWhere(['like', 'crop.crop', $this->crop_idcrops]);
        $query->andFilterWhere(['like', 'estado', $this->estado]);

        return $dataProvider;
    }
}
