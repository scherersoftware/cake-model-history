<?php
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use ModelHistoryTestApp\Model\Entity\User;

/**
 * Users Model
 */
class UsersTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        $this->table('users');
        $this->displayField('firstname');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                [
                    'name' => 'id',
                    'translation' => __('users.id'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'firstname',
                    'translation' => __('users.firstname'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'lastname',
                    'translation' => __('users.lastname'),
                    'searchable' => true,
                    'saveable' => true,
                    'obfuscated' => false,
                    'type' => 'string',
                    'displayParser' => null,
                    'saveParser' => null
                ],
                [
                    'name' => 'article_id',
                    'translation' => __('users.article_id'),
                    'searchable' => true,
                    'saveable' => false,
                    'obfuscated' => false,
                    'type' => 'association'
                ]
            ]
        ]);

        $this->belongsToMany('Articles', [
            'foreignKey' => 'user_id',
            'targetForeignKey' => 'articles_id',
            'joinTable' => 'articles_users'
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
            ->allowEmpty('firstname')
            ->allowEmpty('lastname');

        return $validator;
    }
}
