<?php
declare(strict_types = 1);
namespace ModelHistoryTestApp\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;

/**
 * Items Model
 */
class ItemsTable extends Table
{

    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        $this->table('items');
        $this->displayField('name');
        $this->primaryKey('id');
        $this->addBehavior('Timestamp');

        $this->addBehavior('ModelHistory.Historizable', [
            'userIdCallback' => null,
            'fields' => [
                'articles' => [
                    'translation' => __('items.articles'),
                    'type' => 'mass_association',
                ]
            ],
            'ignoreFields' => []
        ]);

        $this->belongsToMany('Articles', [
            'foreignKey' => 'article_id',
            'targetForeignKey' => 'item_id',
            'joinTable' => 'articles_items'
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
            ->allowEmpty('name');

        return $validator;
    }
}
