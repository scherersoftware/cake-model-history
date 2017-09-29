<?php
declare(strict_types = 1);
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Table;

/**
 * ArticlesUsersTable Model
 */
class ArticlesUsersTable extends Table
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

        $this->table('articles_users');
        $this->displayField('article_id');
        $this->primaryKey(['article_id', 'user_id']);

        $this->addBehavior('ModelHistory.Historizable', [
            'fields' => [
                'article_id' => [
                    'translation' => __('articles_users.article'),
                    'type' => 'association',
                    'associationKey' => 'user_id'
                ],
                'user_id' => [
                    'translation' => __('articles_users.user'),
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
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }
}
