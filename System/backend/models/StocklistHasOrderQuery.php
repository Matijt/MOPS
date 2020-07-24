<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[StocklistHasOrder]].
 *
 * @see StocklistHasOrder
 */
class StocklistHasOrderQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return StocklistHasOrder[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return StocklistHasOrder|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
