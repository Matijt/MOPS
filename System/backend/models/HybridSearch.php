<?php

namespace backend\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Hybrid;

/**
 * HybridSearch represents the model behind the search form of `backend\models\Hybrid`.
 */
class HybridSearch extends Hybrid
{
    public $gpf;
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['idHybrid', 'sowingFemale', 'transplantingMale', 'transplantingFemale', 'pollenColectF', 'pollenColectU', 'pollinitionF', 'pollinitionU', 'harvestF', 'harvestU', 'steamDesinfection'], 'integer'],
            [['variety', 'remarks', 'Crop_idcrops', 'Father_idFather', 'Mother_idMother', 'tsw'], 'safe'],
            ['gpf', 'number'],
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
        $query = Hybrid::find();

        // Join With
        $query->joinWith('motherIdMother');
        $query->joinWith('fatherIdFather');
        $query->joinWith('cropIdcrops');

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);



        // Important: here is how we set up the sorting
        // The key is the attribute name on our "TourSearch" instance
        $dataProvider->sort->attributes['gpf'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['mother.gP' => SORT_ASC],
            'desc' => ['mother.gP' => SORT_DESC],
        ];

        // No search? Then return data Provider
        if (!($this->load($params) && $this->validate())) {
            return $dataProvider;
        }
        // grid filtering conditions
        $query->andFilterWhere([
            'idHybrid' => $this->idHybrid,
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
            'mother.gP' => $this->gpf,
            'tsw' => $this->tsw,
        ]);

        $query
            ->andFilterWhere(['like', 'mother.variety', $this->Mother_idMother])
        ->andFilterWhere(['like', 'hybrid.variety', $this->variety])
        ->andFilterWhere(['like', 'father.variety', $this->Father_idFather])
        ->andFilterWhere(['like', 'crop.crop', $this->Crop_idcrops]);

        if ($this->motherIdMother){
            $query->andFilterWhere(['=', 'mother.gP', $this->motherIdMother->gP]);
        }

        return $dataProvider;
    }
}
