<?php
namespace ModelHistoryTestApp\Model\Entity;

use Cake\ORM\Entity;

/**
 * ArticlesUser Entity.
 */
class ArticlesUser extends Entity
{

    use \ModelHistory\Model\Entity\HistoryContextTrait;

    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        // 'article_id' => false,
        // 'user_id' => false
    ];
}
