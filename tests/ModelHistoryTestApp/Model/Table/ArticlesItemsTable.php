<?php
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ModelHistoryTestApp\Model\Entity\ArticlesItem;

/**
 * ArticlesItemsTable Model
 */
class ArticlesItemsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('articles_items');
        $this->displayField('article_id');
        $this->primaryKey(['article_id', 'item_id']);

        $this->addBehavior('ModelHistory.Historizable', [
            'fields' => [
                [
                    'name' => 'article_id',
                    'translation' => __('articles_items.article'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'association',
                    'associationKey' => 'item_id'
                ],
                [
                    'name' => 'item_id',
                    'translation' => __('articles_items.user'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'association',
                    'associationKey' => 'article_id'
                ]
            ]
        ]);

        $this->belongsTo('Articles', [
            'foreignKey' => 'articles_id',
            'joinType' => 'INNER'
        ]);
        $this->belongsTo('Items', [
            'foreignKey' => 'item_id',
            'joinType' => 'INNER'
        ]);
    }
}
