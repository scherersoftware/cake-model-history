<?php
declare(strict_types = 1);
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Table;

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
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->table('articles_items');
        $this->displayField('article_id');
        $this->primaryKey(['article_id', 'item_id']);

        $this->addBehavior('ModelHistory.Historizable', [
            'fields' => [
                'article_id' => [
                    'translation' => __('articles_items.article'),
                    'type' => 'association',
                    'associationKey' => 'item_id'
                ],
                'item_id' => [
                    'translation' => __('articles_items.user'),
                    'type' => 'association',
                    'associationKey' => 'article_id'
                ]
            ],
            'ignoreFields' => []
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
