<?php
namespace ModelHistoryTestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * Article Entity.
 */
class Article extends Entity
{

    use \ModelHistory\Model\Entity\HistoryContextTrait;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        'title' => true,
        'status' => true,
        'content' => true,
        'json_field' => true,
        'int_field' => true,
    ];
}
