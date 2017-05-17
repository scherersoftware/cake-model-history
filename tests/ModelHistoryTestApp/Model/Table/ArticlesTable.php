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
        $this->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                [
                    'name' => 'id',
                    'translation' => __('articles.id'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'title',
                    'translation' => __('articles.title'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'status',
                    'translation' => __('articles.status'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'content',
                    'translation' => __('articles.content'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'json_field',
                    'translation' => __('articles.json_field'),
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'int_field',
                    'translation' => __('articles.int_field'),
                    'searchable' => false,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'articles_id',
                    'translation' => __('articles.articles_id'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'relation',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'user_id',
                    'translation' => __('articles.user_id'),
                    'searchable' => true,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'association',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'users',
                    'translation' => __('articles.users'),
                    'searchable' => true,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'mass_association',
                    'displayParser' => null,
                    'saveParser' => null
                ]
            ],
            'associated' => [
                'article.article'
            ]
        ]);

        $this->belongsToMany('Users', [
            'foreignKey' => 'article_id',
            'targetForeignKey' => 'user_id',
            'joinTable' => 'articles_users'
        ]);
        $this->belongsToMany('Items');
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
