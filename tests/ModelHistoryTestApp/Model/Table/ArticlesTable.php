<?php
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ModelHistoryTestApp\Model\Entity\Article;

/**
 * Articles Model
 */
class ArticlesTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('articles');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->belongsToMany('Items');
        $this->belongsToMany('Users', [
            'foreignKey' => 'article_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'articles_users'
        ]);

        $this->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                'json_field' => [
                    'searchable' => false,
                    'saveable' => false,
                    'type' => 'string',
                ],
                'int_field' => [
                    'translation' => __('articles.int_field'),
                    'searchable' => false,
                    'saveable' => false,
                    'type' => 'string',
                ],
                'articles_id' => [
                    'translation' => __('articles.articles_id'),
                    'type' => 'relation',
                ],
                'user_id' => [
                    'translation' => __('articles.user_id'),
                    'saveable' => false,
                    'type' => 'association',
                ],
                'users' => [
                    'translation' => __('articles.users'),
                    'saveable' => false,
                    'type' => 'mass_association',
                ]
            ],
            'associated' => [
                'article.article'
            ],
            'ignoreFields' => []
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')
            ->allowEmpty('title')
            ->allowEmpty('status')
            ->allowEmpty('content')
            ->allowEmpty('json_field')
            ->allowEmpty('articles_id')
            ->add('int_field', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('int_field');

        return $validator;
    }
}
