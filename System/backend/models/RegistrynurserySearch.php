<?php

namespace backend\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\models\Registrynursery;

/**
 * RegistrynurserySearch represents the model behind the search form of `backend\models\Registrynursery`.
 */
class RegistrynurserySearch extends Registrynursery
{
    public $compNum;
    public $hybrid;
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'nursery_idnursery', 'shipment', 'seedsRecieved', 'realSeedsRecieved', 'seedsUsed', 'remain', 'order_idorder', 'numPlants', 'plantsPerCompartment', 'seedsReallyGerminated', 'plantsNurseryNeeded', 'remainTray', 'figure_id', 'nursery_idnursery1', 'compNum'], 'integer'],
            [['FM', 'batch', 'prodLot', 'recDate', 'registrynurserycol', 'sowing', 'sowedTable', 'transplant', 'TidelFloor', 'remarks', 'trasplantCompartment', 'hybrid'], 'safe'],
            [['estimatedGermination', 'usedGermination', 'trays', 'germinationReal'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
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
        $query = Registrynursery::find();

        // add conditions that should always apply here

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        // Important: here is how we set up the sorting
        // The key is the attribute name on our "RegistrySearch" instance
        $dataProvider->sort->attributes['compNum'] = [
            // The tables are the ones our relation are configured to
            // in my case they are prefixed with "tbl_"
            'asc' => ['compartment.compNum' => SORT_ASC],
            'desc' => ['compartment.compNum' => SORT_DESC],
        ];
        $dataProvider->sort->attributes['hybrid'] = [
        // The tables are the ones our relation are configured to
        // in my case they are prefixed with "tbl_"
        'asc' => ['hybrid.variety' => SORT_ASC],
        'desc' => ['hybrid.variety  ' => SORT_DESC],
            ];

        $this->load($params);

        if (!$this->validate()) {
            // uncomment the following line if you do not want to return any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->joinWith('orderIdorder');
        $query->joinWith('orderIdorder.compartmentIdCompartment');
        $query->joinWith('orderIdorder.hybridIdHybr');
        $query->joinWith('orderIdorder.hybridIdHybr.fatherIdFather');
        $query->joinWith('orderIdorder.hybridIdHybr.motherIdMother');
        // grid filtering conditions
        $query->andFilterWhere([
            'id' => $this->id,
            'nursery_idnursery' => $this->nursery_idnursery,
            'shipment' => $this->shipment,
            'recDate' => $this->recDate,
            'seedsRecieved' => $this->seedsRecieved,
            'realSeedsRecieved' => $this->realSeedsRecieved,
            'seedsUsed' => $this->seedsUsed,
            'remain' => $this->remain,
            'order_idorder' => $this->order_idorder,
            'numPlants' => $this->numPlants,
            'estimatedGermination' => $this->estimatedGermination,
            'usedGermination' => $this->usedGermination,
            'plantsPerCompartment' => $this->plantsPerCompartment,
            'sowing' => $this->sowing,
            'trays' => $this->trays,
            'seedsReallyGerminated' => $this->seedsReallyGerminated,
            'germinationReal' => $this->germinationReal,
            'transplant' => $this->transplant,
            'plantsNurseryNeeded' => $this->plantsNurseryNeeded,
            'remainTray' => $this->remainTray,
            'figure_id' => $this->figure_id,
            'trasplantCompartment' => $this->trasplantCompartment,
            'nursery_idnursery1' => $this->nursery_idnursery1,
            'compartment.compNum' => $this->compNum,
        ]);

        $query->andFilterWhere(['like', 'FM', $this->FM])
            ->andFilterWhere(['like', 'batch', $this->batch])
            ->andFilterWhere(['like', 'prodLot', $this->prodLot])
            ->andFilterWhere(['like', 'registrynurserycol', $this->registrynurserycol])
            ->andFilterWhere(['like', 'sowedTable', $this->sowedTable])
            ->andFilterWhere(['like', 'TidelFloor', $this->TidelFloor])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'hybrid.variety', $this->hybrid]);

        return $dataProvider;
    }
}
