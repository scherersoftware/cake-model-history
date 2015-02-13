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
        $this->alias('Articles');
        $this->table('articles');
        $this->displayField('title');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');
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
            ->allowEmpty('content')
            ->allowEmpty('json_field')
            ->add('int_field', 'valid', ['rule' => 'numeric'])
            ->allowEmpty('int_field');

        return $validator;
    }
}
