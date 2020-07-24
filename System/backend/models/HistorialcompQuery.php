<?php

namespace backend\models;

/**
 * This is the ActiveQuery class for [[Historialcomp]].
 *
 * @see Historialcomp
 */
class HistorialcompQuery extends \yii\db\ActiveQuery
{
    /*public function active()
    {
        return $this->andWhere('[[status]]=1');
    }*/

    /**
     * @inheritdoc
     * @return Historialcomp[]|array
     */
    public function all($db = null)
    {
        return parent::all($db);
    }

    /**
     * @inheritdoc
     * @return Historialcomp|array|null
     */
    public function one($db = null)
    {
        return parent::one($db);
    }
}
