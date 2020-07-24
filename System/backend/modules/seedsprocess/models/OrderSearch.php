<?php

namespace backend\modules\seedsprocess\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use backend\modules\seedsprocess\models\Order;

/**
 * OrderSearch represents the model behind the search form of `backend\modules\seedsprocess\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['numCrop', 'idorder', 'numRows', 'netNumOfPlantsF', 'netNumOfPlantsM', 'contractNumber', 'sowingM', 'sowingF', 'nurseryM', 'nurseryF', 'realisedNrOfPlantsM', 'realisedNrOfPlantsF', 'extractedPlantsF', 'extractedPlantsM', 'remainingPlantsF', 'remainingPlantsM', 'pollenColectQ', 'compartment_idCompartment', 'plantingDistance'], 'integer'],
            [['orderKg', 'calculatedYield', 'gpOrder'], 'number'],
            [['ReqDeliveryDate', 'orderDate', 'ssRecDate', 'check', 'sowingDateM', 'sowingDateF', 'transplantingM', 'transplantingF', 'pollenColectF', 'pollenColectU', 'pollinationF', 'pollinationU', 'harvestF', 'harvestU', 'steamDesinfectionF', 'steamDesinfectionU', 'remarks', 'state', 'action', 'prueba', 'prueba2', 'Hybrid_idHybrid', 'selector'], 'safe'],
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
        $query = Order::find();

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
            'numCrop' => $this->numCrop,
            'orderKg' => $this->orderKg,
            'idorder' => $this->idorder,
            'numRows' => $this->numRows,
            'gpOrder' => $this->gpOrder,
            'netNumOfPlantsF' => $this->netNumOfPlantsF,
            'netNumOfPlantsM' => $this->netNumOfPlantsM,
            'orderDate' => $this->orderDate,
            'contractNumber' => $this->contractNumber,
            'ssRecDate' => $this->ssRecDate,
            'sowingM' => $this->sowingM,
            'sowingF' => $this->sowingF,
            'nurseryM' => $this->nurseryM,
            'nurseryF' => $this->nurseryF,
            'sowingDateF' => $this->sowingDateF,
            'realisedNrOfPlantsM' => $this->realisedNrOfPlantsM,
            'realisedNrOfPlantsF' => $this->realisedNrOfPlantsF,
            'transplantingM' => $this->transplantingM,
            'transplantingF' => $this->transplantingF,
            'extractedPlantsF' => $this->extractedPlantsF,
            'extractedPlantsM' => $this->extractedPlantsM,
            'remainingPlantsF' => $this->remainingPlantsF,
            'remainingPlantsM' => $this->remainingPlantsM,
            'pollenColectF' => $this->pollenColectF,
            'pollenColectU' => $this->pollenColectU,
            'pollenColectQ' => $this->pollenColectQ,
            'pollinationF' => $this->pollinationF,
            'pollinationU' => $this->pollinationU,
            'harvestF' => $this->harvestF,
            'harvestU' => $this->harvestU,
            'steamDesinfectionF' => $this->steamDesinfectionF,
            'plantingDistance' => $this->plantingDistance,
            'selector' => $this->selector,
        ]);
        if($this->ReqDeliveryDate) {
            $this->ReqDeliveryDate = date('Y-m-d', strtotime($this->ReqDeliveryDate));
        }
        if($this->sowingDateM) {
            $this->sowingDateM = date('Y-m-d', strtotime($this->sowingDateM));
        }
        if($this->steamDesinfectionU) {
            $this->steamDesinfectionU = date('Y-m-d', strtotime($this->steamDesinfectionU));
        }

        $query->joinWith('compartmentIdCompartment');
        $query->joinWith('hybridIdHybr');
        $query->joinWith('hybridIdHybr.fatherIdFather');
        $query->joinWith('hybridIdHybr.motherIdMother');

        $query->andFilterWhere(['like', 'check', $this->check])
            ->andFilterWhere(['like', 'remarks', $this->remarks])
            ->andFilterWhere(['like', 'action', $this->action])
            ->andFilterWhere(['like', 'ReqDeliveryDate', $this->ReqDeliveryDate])
            ->andFilterWhere(['like', 'sowingDateM', $this->sowingDateM])
            ->andFilterWhere(['like', 'steamDesinfectionU', $this->steamDesinfectionU])
            ->andFilterWhere(['like', 'compartment.compnum', $this->compartment_idCompartment])
            ->andFilterWhere(['like', 'state', $this->state])
            ->andFilterWhere(['like', 'hybrid.variety', $this->Hybrid_idHybrid])
        ->andFilterWhere(['like', 'father.variety', $this->prueba])
        ->andFilterWhere(['like', 'mother.variety', $this->prueba2])
        ->andFilterWhere(['like', 'selector', $this->selector])
        ->andFilterWhere(['like', 'gpOrder', $this->gpOrder]);
        IF($this->ReqDeliveryDate) {
            $this->ReqDeliveryDate = date('d-m-Y', strtotime($this->ReqDeliveryDate));
        }
        IF($this->sowingDateM) {
            $this->sowingDateM = date('d-m-Y', strtotime($this->sowingDateM));
        }
        IF($this->steamDesinfectionU) {
            $this->steamDesinfectionU = date('d-m-Y', strtotime($this->steamDesinfectionU));
        }

        return $dataProvider;
    }
}