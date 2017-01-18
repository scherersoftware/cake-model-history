<?php
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ModelHistoryTestApp\Model\Entity\ArticlesUser;

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
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->table('articles_users');
        $this->displayField('article_id');
        $this->primaryKey(['article_id', 'user_id']);

        $this->addBehavior('ModelHistory.Historizable', [
            'fields' => [
                [
                    'name' => 'article_id',
                    'translation' => __('articles_users.article'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'association',
                    'associationKey' => 'user_id'
                ],
                [
                    'name' => 'user_id',
                    'translation' => __('articles_users.user'),
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
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER'
        ]);
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn(['article_id'], 'Articles'));
        $rules->add($rules->existsIn(['user_id'], 'Users'));

        return $rules;
    }
}
