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
                ]
            ]
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
            ->add('int_field', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('int_field');

        return $validator;
    }
}
