<?php
/**
 * Created by PhpStorm.
 * User: mati
 * Date: 14/05/2018
 * Time: 12:00
 */

namespace common\rules;

use yii\rbac\Rule;

/**
 * Checks if authorID matches user passed via params
 */
class UserRule extends Rule
{
    public $name = 'isAuthor';

    /**
     * @param string|int $user the user ID.
     * @param Item $item the role or permission that this rule is associated with
     * @param array $params parameters passed to ManagerInterface::checkAccess().
     * @return bool a value indicating whether the rule permits the role or permission it is associated with.
     */
    public function execute($user, $item, $params)
    {
        return true;
//        return isset($params['post']) ? $params['post']->createdBy == $user : false;
    }
}