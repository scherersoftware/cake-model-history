<?php
declare(strict_types = 1);
namespace ModelHistoryTestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * User Entity.
 */
class User extends Entity
{

    use \ModelHistory\Model\Entity\HistoryContextTrait;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'firstname' => true,
        'lastname' => true
    ];
}
