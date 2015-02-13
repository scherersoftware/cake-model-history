<?php
namespace ModelHistory\Model\Entity;

use Cake\ORM\Entity;

/**
 * ModelHistory Entity.
 */
class ModelHistory extends Entity
{

    const ACTION_CREATE = 'create';
    const ACTION_UPDATE = 'update';
    const ACTION_DELETE = 'delete';

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'model' => true,
        'foreign_key' => true,
        'user_id' => true,
        'action' => true,
        'data' => true,
        'user' => true,
    ];
}
