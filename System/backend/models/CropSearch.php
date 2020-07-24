<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Crop;

/**
 * CropSearch represents the model behind the search form of `backend\models\Crop`.
 */
class CropSearch extends Crop
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idcrops', 'sowingFemale', 'transplantingMale', 'transplantingFemale', 'pollenColectF', 'pollenColectU', 'pollinitionF', 'pollinitionU', 'harvestF', 'harvestU', 'steamDesinfection', 'durationOfTheCrop'], 'integer'],
            [['crop', 'remarks'], 'safe'],
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
        $query = Crop::find();

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
            'idcrops' => $this->idcrops,
            'sowingFemale' => $this->sowingFemale,
            'transplantingMale' => $this->transplantingMale,
            'transplantingFemale' => $this->transplantingFemale,
            'pollenColectF' => $this->pollenColectF,
            'pollenColectU' => $this->pollenColectU,
            'pollinitionF' => $this->pollinitionF,
            'pollinitionU' => $this->pollinitionU,
            'harvestF' => $this->harvestF,
            'harvestU' => $this->harvestU,
            'steamDesinfection' => $this->steamDesinfection,
            'durationOfTheCrop' => $this->durationOfTheCrop,
        ]);

        $query->andFilterWhere(['like', 'crop', $this->crop])
            ->andFilterWhere(['like', 'remarks', $this->remarks]);

        return $dataProvider;
    }
}
