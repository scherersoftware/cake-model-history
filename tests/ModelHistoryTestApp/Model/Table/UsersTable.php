<?php
declare(strict_types = 1);
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

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
    public function initialize(array $config): void
    {
        $this->table('users');
        $this->displayField('firstname');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
        $this->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                'firstname' => [
                    'translation' => __('users.firstname'),
                ],
                'lastname' => [
                    'translation' => __('users.lastname'),
                ],
                'article_id' => [
                    'translation' => __('users.article_id'),
                    'saveable' => false,
                    'type' => 'association'
                ]
            ],
            'ignoreFields' => []
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
    public function validationDefault(Validator $validator): \Cake\Validation\Validator
    {
        $validator
            ->add('id', 'valid', ['rule' => 'uuid'])
            ->allowEmpty('id', 'create')
            ->allowEmpty('firstname')
            ->allowEmpty('lastname');

        return $validator;
    }
}
